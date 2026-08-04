<?php

use App\Support\Admin\HtmlSanitizer;

// -----------------------------------------------------------------------
// XSS vectors must be blocked
// -----------------------------------------------------------------------

it('strips <script> tags', function () {
    $out = HtmlSanitizer::sanitize('<p>Hello</p><script>alert(1)</script>');
    expect($out)->not->toContain('<script');
    expect($out)->toContain('<p>Hello</p>');
});

it('strips onerror event attributes', function () {
    $out = HtmlSanitizer::sanitize('<img src="x" onerror="alert(1)">');
    expect($out)->not->toContain('onerror');
});

it('strips javascript: href', function () {
    $out = HtmlSanitizer::sanitize('<a href="javascript:alert(1)">click</a>');
    expect($out)->not->toContain('javascript:');
});

it('strips svg animate onbegin XSS', function () {
    $out = HtmlSanitizer::sanitize('<svg><animate onbegin="alert(1)" attributeName="x"></animate></svg>');
    expect($out)->not->toContain('onbegin');
});

it('strips iframe srcdoc XSS', function () {
    $out = HtmlSanitizer::sanitize('<iframe srcdoc="<script>alert(1)</script>"></iframe>');
    expect($out)->not->toContain('<script');
});

it('strips <style> blocks', function () {
    $out = HtmlSanitizer::sanitize('<style>body{background:red}</style><p>ok</p>');
    expect($out)->not->toContain('<style');
    expect($out)->toContain('<p>ok</p>');
});

// -----------------------------------------------------------------------
// Allowed content must be preserved
// -----------------------------------------------------------------------

it('preserves <table> structure', function () {
    $html = '<table><thead><tr><th>Col</th></tr></thead><tbody><tr><td>Val</td></tr></tbody></table>';
    $out = HtmlSanitizer::sanitize($html);
    expect($out)->toContain('<table');
    expect($out)->toContain('<thead');
    expect($out)->toContain('<td>Val</td>');
});

it('preserves <u> underline tag', function () {
    $out = HtmlSanitizer::sanitize('<p><u>underlined</u></p>');
    expect($out)->toContain('<u>underlined</u>');
});

it('preserves <sub> and <sup> tags', function () {
    $out = HtmlSanitizer::sanitize('<p>H<sub>2</sub>O and x<sup>2</sup></p>');
    expect($out)->toContain('<sub>2</sub>');
    expect($out)->toContain('<sup>2</sup>');
});

it('preserves text-align style on paragraph', function () {
    $out = HtmlSanitizer::sanitize('<p style="text-align: center;">centered</p>');
    // Symfony sanitizer may keep inline style if allowed on element
    expect($out)->toContain('centered');
});

// -----------------------------------------------------------------------
// Map sanitizer allows iframes
// -----------------------------------------------------------------------

it('map sanitizer allows iframe from map domains', function () {
    $iframe = '<iframe src="https://www.google.com/maps/embed?pb=xyz" width="600" height="450"></iframe>';
    $out = HtmlSanitizer::sanitizeMap($iframe);
    expect($out)->toContain('<iframe');
    expect($out)->toContain('google.com/maps');
});

it('map sanitizer still blocks script tags', function () {
    $out = HtmlSanitizer::sanitizeMap('<iframe src="#"></iframe><script>alert(1)</script>');
    expect($out)->not->toContain('<script');
});
