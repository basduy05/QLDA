<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class TextHelper
{
    /**
     * Parse text for URLs and convert them to clickable links.
     * Also escapes HTML entities to prevent XSS.
     *
     * @param string|null $text
     * @return string
     */
    public static function parseLinks(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Escape the text to prevent XSS
        $escapedText = e($text);

        // Convert URLs to clickable links
        // Using a regex pattern to find URLs
        $pattern = '/(https?:\/\/[^\s]+)/';
        $replacement = '<a href="$1" target="_blank" class="text-accent hover:underline break-words">$1</a>';
        
        $linkedText = preg_replace($pattern, $replacement, $escapedText);

        // Convert newlines to <br> is NOT needed as we rely on white-space: pre-line CSS
        return $linkedText;
    }
}
