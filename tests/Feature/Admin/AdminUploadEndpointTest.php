<?php

use App\Models\User;
use App\Support\Admin\UploadStager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

function makeAdminUser(): User
{
    return User::factory()->create(['role' => 'admin']);
}

// -----------------------------------------------------------------------
// Process (upload staging)
// -----------------------------------------------------------------------

it('processes a valid image upload and returns token', function () {
    $admin = makeAdminUser();
    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $response = $this->actingAs($admin)
        ->post(route('admin.api.upload.process'), ['file' => $file]);

    $response->assertStatus(200);
    $token = $response->getContent();
    expect($token)->toBeString()->not->toBeEmpty();
    expect(str_contains($token, '/'))->toBeFalse(); // no raw path in token
});

it('rejects file with invalid mime type', function () {
    $admin = makeAdminUser();
    $file = UploadedFile::fake()->create('script.php', 10, 'application/x-php');

    $this->actingAs($admin)
        ->post(route('admin.api.upload.process'), ['file' => $file])
        ->assertStatus(422);
});

it('rejects file over max size', function () {
    $admin = makeAdminUser();
    $file = UploadedFile::fake()->create('large.jpg', 9000, 'image/jpeg'); // 9MB

    $this->actingAs($admin)
        ->post(route('admin.api.upload.process'), ['file' => $file])
        ->assertStatus(422);
});

it('guest cannot upload', function () {
    $file = UploadedFile::fake()->image('photo.jpg');
    $this->post(route('admin.api.upload.process'), ['file' => $file])
        ->assertRedirectToRoute('admin.login');
});

// -----------------------------------------------------------------------
// Revert
// -----------------------------------------------------------------------

it('revert with valid same-session token removes tmp file', function () {
    // Test UploadStager directly: HTTP session mismatch is tested via the 403 test.
    // The controller's revert action is a thin wrapper around UploadStager::revert().
    $file = UploadedFile::fake()->image('revert-test.jpg', 50, 50);
    $sessionId = 'test-revert-session';

    $token = UploadStager::stage($file, $sessionId);
    expect(Storage::disk('local')->allFiles())->not->toBeEmpty();

    UploadStager::revert($token, $sessionId);
    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('revert with raw path returns 422', function () {
    $admin = makeAdminUser();

    $this->actingAs($admin)
        ->call('DELETE', route('admin.api.upload.revert'), [], [], [], ['CONTENT_TYPE' => 'text/plain'], '../some/path.jpg')
        ->assertStatus(422);
});

it('revert with token from different session returns 403', function () {
    $admin = makeAdminUser();

    // Create a token for a different session
    $fakeSession = 'different-session-id';
    $file = UploadedFile::fake()->image('other.jpg');

    // Manually stage to a different session using UploadStager directly
    $token = UploadStager::stage($file, $fakeSession);

    // Try to revert via endpoint (which uses the real session, not fakeSession)
    $this->actingAs($admin)
        ->call('DELETE', route('admin.api.upload.revert'), [], [], [], ['CONTENT_TYPE' => 'text/plain'], $token)
        ->assertStatus(403);
});
