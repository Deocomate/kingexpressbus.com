<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Support\Admin\UploadStager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UploadController extends Controller
{
    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
    ];

    private const MAX_SIZE_KB = 8192; // 8 MB

    public function process(Request $request): Response|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'max:' . self::MAX_SIZE_KB,
                'mimetypes:' . implode(',', self::ALLOWED_MIMES),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $sessionId = $request->session()->getId();

        $token = UploadStager::stage($file, $sessionId);

        // FilePond expects plain-text server response containing the token
        return response($token, 200)->header('Content-Type', 'text/plain');
    }

    public function revert(Request $request): Response|JsonResponse
    {
        $token = trim($request->getContent());

        if (empty($token)) {
            return response()->json(['error' => 'Token is required.'], 422);
        }

        // Quick raw path detection before token parsing
        if (str_contains($token, '/') || str_contains($token, '\\') || str_contains($token, '..')) {
            return response()->json(['error' => 'Invalid token format.'], 422);
        }

        $sessionId = $request->session()->getId();

        try {
            UploadStager::revert($token, $sessionId);
        } catch (AccessDeniedHttpException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (UnprocessableEntityHttpException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response('', 204);
    }
}
