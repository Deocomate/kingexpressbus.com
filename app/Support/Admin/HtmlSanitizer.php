<?php

namespace App\Support\Admin;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonySanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Wraps symfony/html-sanitizer.
 * Sanitize on render (never on save) to preserve stored content fidelity.
 *
 * Two configs:
 *   - default: rich editor output (table, formatting, align etc.)
 *   - map: additionally allows <iframe> from known map domains
 */
class HtmlSanitizer
{
    private static ?SymfonySanitizer $defaultSanitizer = null;
    private static ?SymfonySanitizer $mapSanitizer = null;

    /** Sanitize rich editor HTML for public display. */
    public static function sanitize(string $html): string
    {
        return self::getDefault()->sanitize($html);
    }

    /** Sanitize map_embedded fields that may contain map <iframe>s. */
    public static function sanitizeMap(string $html): string
    {
        return self::getMap()->sanitize($html);
    }

    // -------------------------------------------------------------------------

    private static function getDefault(): SymfonySanitizer
    {
        if (self::$defaultSanitizer === null) {
            $config = self::baseConfig();
            self::$defaultSanitizer = new SymfonySanitizer($config);
        }
        return self::$defaultSanitizer;
    }

    private static function getMap(): SymfonySanitizer
    {
        if (self::$mapSanitizer === null) {
            $config = self::baseConfig()
                ->allowElement('iframe', [
                    'src', 'width', 'height', 'frameborder', 'allowfullscreen',
                    'loading', 'referrerpolicy', 'style',
                ])
                ->allowLinkSchemes(['https', 'http'])
                ->allowMediaSchemes(['https', 'http']);
            self::$mapSanitizer = new SymfonySanitizer($config);
        }
        return self::$mapSanitizer;
    }

    private static function baseConfig(): HtmlSanitizerConfig
    {
        return (new HtmlSanitizerConfig())
            ->allowSafeElements()
            // Tiptap rich editor tags
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('sub')
            ->allowElement('sup')
            ->allowElement('del')
            ->allowElement('mark')
            ->allowElement('abbr', ['title'])
            ->allowElement('code')
            ->allowElement('pre')
            ->allowElement('blockquote')
            ->allowElement('h1')
            ->allowElement('h2')
            ->allowElement('h3')
            ->allowElement('h4')
            ->allowElement('h5')
            ->allowElement('h6')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('hr')
            ->allowElement('strong')
            ->allowElement('em')
            ->allowElement('b')
            ->allowElement('i')
            // Tables (Tiptap table extension)
            ->allowElement('table', ['style'])
            ->allowElement('thead')
            ->allowElement('tbody')
            ->allowElement('tfoot')
            ->allowElement('tr')
            ->allowElement('th', ['colspan', 'rowspan', 'style'])
            ->allowElement('td', ['colspan', 'rowspan', 'style'])
            ->allowElement('colgroup')
            ->allowElement('col', ['span'])
            // Links
            ->allowElement('a', ['href', 'target', 'rel', 'title'])
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            // Allow style attribute on block elements for text-align
            ->allowElement('div', ['style', 'class'])
            ->allowElement('span', ['style', 'class'])
            // Images
            ->allowElement('img', ['src', 'alt', 'width', 'height', 'title', 'loading'])
            ->allowMediaSchemes(['https', 'http']);
            // allowSafeElements() already strips script, style, on* handlers, iframe by default
    }
}
