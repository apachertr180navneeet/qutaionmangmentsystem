<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class JaquarProductImageService
{
    protected string $searchBaseUrl = 'https://www.jaquar.com/en/search';
    protected string $storageFolder = 'uploads/items';
    protected int $maxRetries = 3;
    protected int $retryDelayMs = 1000;

    /**
     * Download and save product image for given SKU from Jaquar website.
     *
     * @param string $sku
     * @return string|null Relative path to saved image (e.g. 'uploads/items/SKU.jpg') or null on failure.
     * @throws Exception
     */
    public function fetchAndSaveImageForSku(string $sku): ?string
    {
        $cleanSku = trim($sku);
        if (empty($cleanSku)) {
            throw new Exception("SKU is empty.");
        }

        // Safe filename for SKU (keep alphanumeric, hyphens, and underscores)
        $safeSku = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $cleanSku);

        // 1. Search Jaquar for SKU
        $searchUrl = $this->searchBaseUrl . '?q=' . urlencode($cleanSku);

        $response = Http::retry($this->maxRetries, $this->retryDelayMs)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            ])
            ->timeout(15)
            ->get($searchUrl);

        if (!$response->successful()) {
            throw new Exception("HTTP Search Request failed for SKU {$cleanSku} with status {$response->status()}");
        }

        $html = $response->body();

        // 2. Extract image URL from HTML response
        $imageUrl = $this->extractImageUrlFromHtml($html, 'https://www.jaquar.com');

        if (!$imageUrl) {
            throw new Exception("No product image found on Jaquar for SKU {$cleanSku}");
        }

        // 3. Download image content
        $imageResponse = Http::retry($this->maxRetries, $this->retryDelayMs)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])
            ->timeout(20)
            ->get($imageUrl);

        if (!$imageResponse->successful()) {
            throw new Exception("Failed to download image from {$imageUrl} for SKU {$cleanSku}. HTTP status {$imageResponse->status()}");
        }

        $imageContent = $imageResponse->body();

        // 4. Validate image content
        if (!$this->isValidImageContent($imageContent)) {
            throw new Exception("Downloaded content for SKU {$cleanSku} from {$imageUrl} is not a valid image.");
        }

        // 5. Save image to uploads/items/{SKU}.jpg (public_path and Storage public disk)
        $relativeFilePath = "uploads/items/{$safeSku}.jpg";

        // Write to public_path('uploads/items')
        $publicDir = public_path('uploads/items');
        if (!file_exists($publicDir)) {
            @mkdir($publicDir, 0755, true);
        }
        @file_put_contents(public_path($relativeFilePath), $imageContent);

        // Also write to Storage::disk('public')
        Storage::disk('public')->put($relativeFilePath, $imageContent);

        return $relativeFilePath;
    }

    /**
     * Extract main product image URL from HTML.
     */
    public function extractImageUrlFromHtml(string $html, string $baseUrl): ?string
    {
        if (empty(trim($html))) {
            return null;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Option A: Check OpenGraph og:image meta tag
        $ogNodes = $xpath->query('//meta[@property="og:image"]/@content | //meta[@name="og:image"]/@content');
        if ($ogNodes && $ogNodes->length > 0) {
            $src = trim($ogNodes->item(0)->nodeValue);
            if (!empty($src) && !str_contains($src, 'placeholder') && !str_contains($src, 'logo')) {
                return $this->normalizeUrl($src, $baseUrl);
            }
        }

        // Option B: Search for product main image element (common selectors / attributes on Jaquar store)
        $queries = [
            '//div[contains(@class, "product")]//img/@src',
            '//div[contains(@class, "product")]//img/@data-src',
            '//img[contains(@class, "product")]/@src',
            '//img[contains(@class, "main-image")]/@src',
            '//div[contains(@class, "gallery")]//img/@src',
            '//a[contains(@class, "product-item")]//img/@src',
            '//img/@src',
        ];

        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes && $nodes->length > 0) {
                foreach ($nodes as $node) {
                    $src = trim($node->nodeValue);
                    if ($this->isValidImageUrlCandidate($src)) {
                        return $this->normalizeUrl($src, $baseUrl);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if a candidate image URL is plausible.
     */
    protected function isValidImageUrlCandidate(string $src): bool
    {
        if (empty($src)) {
            return false;
        }
        if (str_starts_with($src, 'data:image')) {
            return false;
        }
        $lower = strtolower($src);
        if (str_contains($lower, 'logo') || str_contains($lower, 'icon') || str_contains($lower, 'banner') || str_contains($lower, 'placeholder')) {
            return false;
        }

        return true;
    }

    /**
     * Normalize URL (handle relative vs absolute URLs).
     */
    protected function normalizeUrl(string $url, string $baseUrl): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (str_starts_with($url, '/')) {
            return rtrim($baseUrl, '/') . $url;
        }

        return rtrim($baseUrl, '/') . '/' . $url;
    }

    /**
     * Validate binary image buffer.
     */
    public function isValidImageContent(string $content): bool
    {
        if (strlen($content) < 10) {
            return false;
        }

        $imageInfo = @getimagesizefromstring($content);
        if ($imageInfo !== false) {
            return true;
        }

        // Fallback mime header check for common image formats (JPEG, PNG, WEBP, GIF)
        $header = substr($content, 0, 12);
        if (str_starts_with($header, "\xFF\xD8\xFF")) { // JPEG
            return true;
        }
        if (str_starts_with($header, "\x89PNG\r\n\x1a\n")) { // PNG
            return true;
        }
        if (str_starts_with($header, 'GIF87a') || str_starts_with($header, 'GIF89a')) { // GIF
            return true;
        }
        if (str_contains($header, 'WEBP')) { // WEBP
            return true;
        }

        return false;
    }
}
