<?php

namespace App\Support\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Two-phase upload: stage to admin-tmp/{session}/{uuid}/ outside public disk,
 * return a signed token; commit to target directory only on successful form save.
 */
class UploadStager
{
    private const TMP_DISK = 'local';
    private const TMP_BASE = 'admin-tmp';
    private const TOKEN_ALGO = 'sha256';

    /** Store uploaded file in tmp and return a signed token. */
    public static function stage(UploadedFile $file, string $sessionId): string
    {
        $uuid = Str::uuid()->toString();
        $ext = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;

        $dir = self::TMP_BASE . '/' . $sessionId . '/' . $uuid;
        Storage::disk(self::TMP_DISK)->makeDirectory($dir);
        $file->storeAs($dir, $filename, ['disk' => self::TMP_DISK]);

        return self::signToken($sessionId, $uuid, $filename);
    }

    /**
     * Validate token belongs to current session, delete tmp file.
     * @throws AccessDeniedHttpException if token belongs to another session
     * @throws UnprocessableEntityHttpException if token is a raw path (not token format)
     */
    public static function revert(string $token, string $sessionId): void
    {
        // verifyToken() handles raw path and signature validation
        $payload = self::verifyToken($token);

        if ($payload['session'] !== $sessionId) {
            throw new AccessDeniedHttpException('Token does not belong to this session.');
        }

        $dir = self::TMP_BASE . '/' . $payload['session'] . '/' . $payload['uuid'];
        Storage::disk(self::TMP_DISK)->deleteDirectory($dir);
    }

    /**
     * Move staged file from tmp into the public disk target directory.
     * Returns the relative path on the public disk.
     * @throws RuntimeException if token is invalid or file not found
     */
    public static function commit(string $token, string $targetDirectory, string $sessionId): string
    {
        $payload = self::verifyToken($token);

        if ($payload['session'] !== $sessionId) {
            throw new RuntimeException('Token session mismatch during commit.');
        }

        $dir = self::TMP_BASE . '/' . $payload['session'] . '/' . $payload['uuid'];
        $tmpPath = $dir . '/' . $payload['filename'];

        if (! Storage::disk(self::TMP_DISK)->exists($tmpPath)) {
            throw new RuntimeException("Staged file not found: {$tmpPath}");
        }

        $targetDirectory = trim($targetDirectory, '/');
        $destPath = $targetDirectory . '/' . $payload['filename'];

        // Ensure unique filename in target
        if (Storage::disk('public')->exists($destPath)) {
            $base = pathinfo($payload['filename'], PATHINFO_FILENAME);
            $ext = pathinfo($payload['filename'], PATHINFO_EXTENSION);
            $destPath = $targetDirectory . '/' . $base . '-' . Str::random(6) . '.' . $ext;
        }

        $contents = Storage::disk(self::TMP_DISK)->get($tmpPath);
        Storage::disk('public')->put($destPath, $contents);

        // Clean up tmp
        Storage::disk(self::TMP_DISK)->deleteDirectory($dir);

        return $destPath;
    }

    /** Delete a committed public file (when removed from model). */
    public static function delete(string $relativePath): void
    {
        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    // -------------------------------------------------------------------------

    private static function signToken(string $session, string $uuid, string $filename): string
    {
        // Use URL-safe base64 to avoid '/' in token (which would look like a path)
        $payload = rtrim(strtr(base64_encode(json_encode(['s' => $session, 'u' => $uuid, 'f' => $filename])), '+/', '-_'), '=');
        $sig = hash_hmac(self::TOKEN_ALGO, $payload, config('app.key'));
        return $payload . '~' . $sig; // use '~' as separator (not '.') to avoid confusion with filenames
    }

    private static function verifyToken(string $token): array
    {
        // Detect raw path: contains / or \ separators
        if (str_contains($token, '/') || str_contains($token, '\\')) {
            throw new UnprocessableEntityHttpException('Invalid token format: raw path not accepted.');
        }

        $parts = explode('~', $token, 2);
        if (count($parts) !== 2) {
            throw new RuntimeException('Malformed upload token.');
        }
        [$payload, $sig] = $parts;
        $expected = hash_hmac(self::TOKEN_ALGO, $payload, config('app.key'));
        if (! hash_equals($expected, $sig)) {
            throw new RuntimeException('Upload token signature invalid.');
        }
        // Decode URL-safe base64
        $json = base64_decode(strtr($payload, '-_', '+/'));
        $data = json_decode($json, true);
        if (! isset($data['s'], $data['u'], $data['f'])) {
            throw new RuntimeException('Upload token payload malformed.');
        }
        return ['session' => $data['s'], 'uuid' => $data['u'], 'filename' => $data['f']];
    }
}
