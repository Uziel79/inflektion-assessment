<?php

namespace App\Services;

use Html2Text\Html2Text;

class EmailParsingService
{
    public function extractPlainText($rawEmail)
    {
        // Check if the content is HTML
        if (strpos($rawEmail, '<html') !== false || strpos($rawEmail, '<body') !== false) {
            // If it's HTML, use Html2Text to convert it
            $html2text = new Html2Text($rawEmail);
            return $html2text->getText();
        }

        // If it's not HTML, assume it's a full email structure
        // Split the email into headers and body
        $parts = explode("\r\n\r\n", $rawEmail, 2);

        if (count($parts) < 2) {
            // If we can't split into headers and body, return the original content
            return $rawEmail;
        }

        list($headers, $body) = $parts;

        // Check if the email is multipart
        if (preg_match('/Content-Type: multipart\/alternative;.*boundary="([^"]+)"/s', $headers, $matches)) {
            $boundary = $matches[1];
            $parts = explode("--$boundary", $body);

            // Look for the plain text part
            foreach ($parts as $part) {
                if (strpos($part, 'Content-Type: text/plain') !== false) {
                    // Extract the content after the headers
                    list(, $content) = explode("\r\n\r\n", $part, 2);
                    return trim($content);
                }
            }
        }

        // If no plain text part found, convert the whole body from HTML to text
        $html2text = new Html2Text($body);
        return $html2text->getText();
    }

    public function sanitizePlainText($text)
    {
        return preg_replace('/[^\P{C}\n]+/u', '', $text);
    }
}
