<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Small allow-list HTML sanitizer for rich text written in the browser editor
 * (doctor "About Me"). Keeps formatting tags and a few safe inline styles,
 * drops scripts, event handlers and dangerous URLs.
 */
class HtmlSanitizer
{
    /** Tags that are kept (with their allowed attributes). */
    private const TAGS = [
        'p' => [], 'br' => [], 'div' => [], 'span' => [], 'hr' => [],
        'b' => [], 'strong' => [], 'i' => [], 'em' => [], 'u' => [], 's' => [], 'strike' => [],
        'sub' => [], 'sup' => [], 'small' => [], 'mark' => [],
        'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'pre' => [], 'code' => [],
        'a' => ['href', 'target'],
        'table' => [], 'thead' => [], 'tbody' => [], 'tfoot' => [], 'tr' => [],
        'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
        'font' => ['color', 'size', 'face'],
    ];

    /** Tags removed together with everything inside them. */
    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'select',
        'textarea', 'svg', 'math', 'link', 'meta', 'base', 'frame', 'frameset', 'noscript', 'template',
    ];

    /** CSS properties allowed in style="" (formatting only). */
    private const STYLES = [
        'color', 'background-color', 'font-size', 'font-family', 'font-weight', 'font-style',
        'text-align', 'text-decoration', 'line-height', 'margin-left', 'padding-left', 'text-indent',
    ];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML(
            '<?xml encoding="UTF-8"><div id="__root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementById('__root');
        if (!$root) {
            return null;
        }

        self::cleanChildren($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        $out = trim($out);

        // Nothing but empty paragraphs / line breaks counts as empty.
        $text = trim(html_entity_decode(strip_tags($out), ENT_QUOTES | ENT_HTML5, 'UTF-8'), " \t\n\r\0\x0B\xC2\xA0");
        return $text === '' ? null : $out;
    }

    /** Plain text from older profiles becomes paragraphs; HTML is sanitized. */
    public static function fromStored(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        if ($value === strip_tags($value)) {
            $paragraphs = preg_split("/\R{2,}/", trim($value));
            return implode('', array_map(fn ($p) => '<p>' . nl2br(e(trim($p)), false) . '</p>', $paragraphs));
        }
        return self::clean($value);
    }

    private static function cleanChildren(DOMNode $node): void
    {
        // copy first: the list changes while we edit it
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue; // text
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            self::cleanChildren($child);

            if (!array_key_exists($tag, self::TAGS)) {
                // unknown tag: keep its content, drop the tag
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            self::cleanAttributes($child, self::TAGS[$tag]);
        }
    }

    private static function cleanAttributes(DOMElement $el, array $allowed): void
    {
        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->name);
            $value = trim($attr->value);

            if ($name === 'style') {
                $style = self::cleanStyle($value);
                $style === '' ? $el->removeAttribute($attr->name) : $el->setAttribute('style', $style);
                continue;
            }

            if (!in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);
                continue;
            }

            if ($name === 'href' && !preg_match('~^(https?://|mailto:|tel:|/|#)~i', $value)) {
                $el->removeAttribute($attr->name);
            } elseif ($name === 'target') {
                $el->setAttribute('target', '_blank');
            } elseif (in_array($name, ['colspan', 'rowspan', 'size'], true) && !ctype_digit($value)) {
                $el->removeAttribute($attr->name);
            } elseif (in_array($name, ['color', 'face'], true) && !preg_match('~^[#\w\s,\-\'"]+$~', $value)) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($el->hasAttribute('target')) {
            $el->setAttribute('rel', 'noopener noreferrer nofollow');
        }
    }

    private static function cleanStyle(string $style): string
    {
        $kept = [];
        foreach (explode(';', $style) as $rule) {
            if (!str_contains($rule, ':')) {
                continue;
            }
            [$prop, $val] = array_map('trim', explode(':', $rule, 2));
            $prop = strtolower($prop);
            if (!in_array($prop, self::STYLES, true)) {
                continue;
            }
            if ($val === '' || preg_match('~url\s*\(|expression|javascript:|[<>\\\\]~i', $val)) {
                continue;
            }
            $kept[] = $prop . ': ' . $val;
        }
        return implode('; ', $kept);
    }
}
