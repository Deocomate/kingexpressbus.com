<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tiptap\Editor as TiptapEditor;
use Tiptap\Extensions\StarterKit;

uses(RefreshDatabase::class);

/**
 * Verifies that the Tiptap extensions selected for Phase 3 can round-trip the actual
 * rich text content without altering it.  Tests skip when tiptap-php is unavailable
 * or when the test DB has no content rows (run against real DB data to get full coverage).
 */

function tiptapAvailable(): bool
{
    return class_exists(TiptapEditor::class);
}

function makeTiptapEditor(): TiptapEditor
{
    // StarterKit is the default in v2.0.0 when no extensions are passed.
    return new TiptapEditor(['extensions' => [new StarterKit]]);
}

it('round-trips routes.content through tiptap-php without mutation', function () {
    if (! tiptapAvailable()) {
        $this->markTestSkipped('tiptap-php not installed — install ueberdosis/tiptap-php:^2 to run.');
    }

    $rows = DB::table('routes')->whereNotNull('content')->where('content', '!=', '')->get(['id', 'content']);

    if ($rows->isEmpty()) {
        $this->markTestSkipped('No route content rows in test DB — seed or run against real DB.');
    }

    $editor = makeTiptapEditor();

    foreach ($rows as $row) {
        $parsed = $editor->setContent($row->content)->getHTML();
        // Normalise whitespace differences that tiptap may introduce around block elements.
        $normalize = fn (string $html) => preg_replace('/\s+/', ' ', trim($html));
        expect($normalize($parsed))->toBe($normalize($row->content), "Route ID {$row->id}: content changed after round-trip.");
    }
})->skip(fn () => ! tiptapAvailable(), 'tiptap-php not installed');

it('round-trips web_profiles rich text through tiptap-php without mutation', function () {
    if (! tiptapAvailable()) {
        $this->markTestSkipped('tiptap-php not installed — install ueberdosis/tiptap-php:^2 to run.');
    }

    $wp = DB::table('web_profiles')->first();
    if (! $wp) {
        $this->markTestSkipped('No web_profiles rows in test DB.');
    }

    $editor = makeTiptapEditor();
    $normalize = fn (string $html) => preg_replace('/\s+/', ' ', trim($html));

    foreach (['policy_content', 'introduction_content'] as $field) {
        $val = $wp->$field ?? '';
        if (empty($val)) {
            continue;
        }
        $parsed = $editor->setContent($val)->getHTML();
        expect($normalize($parsed))->toBe($normalize($val), "web_profiles.{$field}: content changed after round-trip.");
    }
})->skip(fn () => ! tiptapAvailable(), 'tiptap-php not installed');
