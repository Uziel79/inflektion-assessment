<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SuccessfulEmail;
use Illuminate\Http\Request;
use Html2Text\Html2Text;

class SuccessfulEmailController extends Controller
{
    public function index()
    {
        return SuccessfulEmail::paginate(15);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'affiliate_id' => 'required|integer',
            'envelope' => 'required|string',
            'from' => 'required|string',
            'subject' => 'required|string',
            'dkim' => 'nullable|string',
            'SPF' => 'nullable|string',
            'spam_score' => 'nullable|numeric',
            'email' => 'required|string',
            'sender_ip' => 'nullable|string',
            'to' => 'required|string',
            'timestamp' => 'required|integer',
        ]);

        $plainText = $this->extractPlainText($validatedData['email']);
        $plainText = preg_replace('/[^\P{C}\n]+/u', '', $plainText);

        // Only set raw_text if it's not empty
        if (!empty($plainText)) {
            $validatedData['raw_text'] = $plainText;
        }

        return SuccessfulEmail::create($validatedData);
    }

    public function show(SuccessfulEmail $successfulEmail)
    {
        return $successfulEmail;
    }

    public function update(Request $request, SuccessfulEmail $successfulEmail)
    {
        $validatedData = $request->validate([
            'affiliate_id' => 'integer',
            'envelope' => 'string',
            'from' => 'string',
            'subject' => 'string',
            'dkim' => 'nullable|string',
            'SPF' => 'nullable|string',
            'spam_score' => 'nullable|numeric',
            'email' => 'string',
            'sender_ip' => 'nullable|string',
            'to' => 'string',
            'timestamp' => 'integer',
        ]);

        if (isset($validatedData['email'])) {
            $html = $validatedData['email'];
            $text = new Html2Text($html);
            $plainText = $text->getText();
            $plainText = preg_replace('/[^\P{C}\n]+/u', '', $plainText);
            $validatedData['raw_text'] = $plainText;
        }

        $successfulEmail->update($validatedData);
        return $successfulEmail;
    }

    public function destroy(SuccessfulEmail $successfulEmail)
    {
        $successfulEmail->delete();
        return response()->json(null, 204);
    }

    private function extractPlainText($rawEmail)
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
}
