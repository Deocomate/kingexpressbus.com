<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

function createStagingDir(string $session, string $uuid, int $mtimeDiff = 0): string
{
    $dir = "admin-tmp/{$session}/{$uuid}";
    Storage::disk('local')->makeDirectory($dir);
    Storage::disk('local')->put("{$dir}/file.jpg", 'fake-content');
    return $dir;
}

it('deletes directories older than 24 hours', function () {
    $session = 'old-session';
    $uuid = 'old-uuid';
    $dir = createStagingDir($session, $uuid);

    // Travel 25 hours into the future so the cutoff is now() + 25h,
    // which is definitely > the file's mtime
    $this->travel(25)->hours();

    $this->artisan('admin:prune-upload-staging', ['--hours' => 24])
        ->assertExitCode(0)
        ->expectsOutputToContain('Pruned');

    expect(Storage::disk('local')->exists("{$dir}/file.jpg"))->toBeFalse();
});

it('keeps directories newer than threshold', function () {
    $session = 'new-session';
    $uuid = 'new-uuid';
    $dir = createStagingDir($session, $uuid);

    // Use a very high threshold so nothing gets deleted
    $this->artisan('admin:prune-upload-staging', ['--hours' => 9999])
        ->assertExitCode(0);

    // File should still exist
    expect(Storage::disk('local')->exists("{$dir}/file.jpg"))->toBeTrue();
});

it('handles missing admin-tmp directory gracefully', function () {
    // Storage is already fake and empty — no admin-tmp
    $this->artisan('admin:prune-upload-staging')
        ->assertExitCode(0)
        ->expectsOutputToContain('nothing to prune');
});

it('is scheduled to run daily', function () {
    $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
    $events = collect($schedule->events())
        ->filter(fn ($e) => str_contains($e->command ?? '', 'prune-upload-staging'));

    expect($events->count())->toBeGreaterThanOrEqual(1);
});
