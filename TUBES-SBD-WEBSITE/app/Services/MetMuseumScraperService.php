<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class MetMuseumScraperService
{
    /**
     * Get artwork description from a specific URL.
     *
     * @param string $url
     * @return string|null
     */
    public function getDescriptionFromUrl($url)
    {
        // 4. Validasi URL
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL) || strpos($url, 'metmuseum.org') === false) {
            return null;
        }

        // Force HTTPS
        $url = str_replace('http://', 'https://', $url);

        Log::info("[SCRAPE] DEBUG START: {$url}");

        try {
            // 1. Request using shell_exec with curl.exe (more successful at bypassing Vercel blocks on Windows)
            $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';
            $cmd = "curl.exe -k -s -L -A \"{$ua}\" --compressed \"{$url}\"";
            $html = shell_exec($cmd);

            if (empty($html)) {
                Log::error("[SCRAPE] FAILED URL: {$url}. Shell_exec returned empty result.");
                return null;
            }

            $htmlLength = strlen($html);
            $status = (strpos($html, 'verify your browser') !== false || strpos($html, 'Security Checkpoint') !== false) ? 429 : 200;

            // 2. Save raw HTML for debugging (even if it failed)
            $debugFile = strpos($url, '310509') !== false ? 'debug_met_310509.html' : 'debug_met.html';
            file_put_contents(storage_path("app/{$debugFile}"), $html);

            // 3. Log initial info
            Log::info("[SCRAPE] HTTP Status: {$status}");
            Log::info("[SCRAPE] HTML Length: {$htmlLength}");
            Log::info("[SCRAPE] Contains read-more-content: " . (stripos($html, 'read-more-content') !== false ? 'YES' : 'NO'));
            Log::info("[SCRAPE] Contains __NEXT_DATA__: " . (stripos($html, '__NEXT_DATA__') !== false ? 'YES' : 'NO'));
            Log::info("[SCRAPE] Contains application/ld+json: " . (stripos($html, 'application/ld+json') !== false ? 'YES' : 'NO'));

            if ($status === 429) {
                Log::error("[SCRAPE] FAILED URL: {$url}. Status: 429. Blocked by security checkpoint.");
                return null;
            }

            $crawler = new Crawler($html);

            // 4. Test HTML Selectors
            $selectors = [
                'read-more' => '[data-testid="read-more-content"]',
                'intro' => '.artwork__intro',
                'content' => '.artwork__content'
            ];

            foreach ($selectors as $name => $selector) {
                $node = $crawler->filter($selector);
                $count = $node->count();
                Log::info("[SCRAPE] Selector {$name} count: {$count}");
                
                if ($count > 0) {
                    $rawText = $node->text();
                    Log::info("[SCRAPE] Selector {$name} text length: " . strlen($rawText));
                    Log::info("[SCRAPE] Selector {$name} preview: " . substr($rawText, 0, 200));

                    $description = $this->cleanHtml($node->html());
                    if (!empty($description)) {
                        $this->logSuccess($description, "html selector ({$name})");
                        return $description;
                    }
                }
            }

            // 5. Hydration Search: __NEXT_DATA__
            $nextDataNode = $crawler->filter('script#__NEXT_DATA__');
            if ($nextDataNode->count() > 0) {
                Log::info("[SCRAPE] Found __NEXT_DATA__ script");
                $jsonData = json_decode($nextDataNode->text(), true);
                if ($jsonData) {
                    $description = $this->searchJsonKey($jsonData, ['description', 'text', 'artworkDescription', 'content', 'seoDescription', 'articleBody', 'body']);
                    if ($description) {
                        $cleaned = $this->cleanHtml($description);
                        $this->logSuccess($cleaned, "hydration json (__NEXT_DATA__)");
                        return $cleaned;
                    }
                }
            }

            // 5. Hydration Search: application/ld+json
            $ldJsonNodes = $crawler->filter('script[type="application/ld+json"]');
            if ($ldJsonNodes->count() > 0) {
                Log::info("[SCRAPE] Found application/ld+json scripts (" . $ldJsonNodes->count() . ")");
                foreach ($ldJsonNodes as $node) {
                    $jsonData = json_decode($node->nodeValue, true);
                    if ($jsonData) {
                        $description = $this->searchJsonKey($jsonData, ['description', 'text', 'articleBody', 'caption']);
                        if ($description) {
                            $cleaned = $this->cleanHtml($description);
                            $this->logSuccess($cleaned, "hydration json (ld+json)");
                            return $cleaned;
                        }
                    }
                }
            }

            // Fallback Regex
            if (preg_match('/data-testid="read-more-content".*?<div><div>(.*?)<\/div><\/div>/s', $html, $matches)) {
                $description = $this->cleanHtml($matches[1]);
                if (!empty($description)) {
                    $this->logSuccess($description, "regex fallback");
                    return $description;
                }
            }

            Log::warning("[SCRAPE] No description found for URL: {$url}");
            return null;

        } catch (\Exception $e) {
            Log::error("[SCRAPE] ERROR for URL: {$url}. Message: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Recursive search for specific keys in array.
     */
    private function searchJsonKey($data, $targetKeys)
    {
        if (!is_array($data)) return null;

        // Priority 1: Check exact matches in current level
        foreach ($targetKeys as $key) {
            if (isset($data[$key]) && is_string($data[$key]) && strlen($data[$key]) > 20) {
                return $data[$key];
            }
        }

        // Priority 2: Recurse
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $res = $this->searchJsonKey($value, $targetKeys);
                if ($res) return $res;
            }
        }

        // Priority 3: Case-insensitive fuzzy search for "description" or "body"
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($key) && (stripos($key, 'description') !== false || stripos($key, 'body') !== false)) {
                    if (is_string($value) && strlen($value) > 20) {
                        return $value;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Clean and format scraped HTML description.
     */
    private function cleanHtml($html)
    {
        if (empty($html)) return '';

        // 1. Convert <br> to newline
        $content = preg_replace('/<br\s*\/?>/i', "\n", $html);
        
        // 2. Strip tags
        $content = strip_tags($content);
        
        // 4. Normalization & Sanitization
        $description = html_entity_decode(
            $content,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Convert to UTF-8 and ignore invalid bytes
        $description = mb_convert_encoding($description, 'UTF-8', 'UTF-8');

        if (function_exists('iconv')) {
            $description = iconv('UTF-8', 'UTF-8//IGNORE', $description);
        }

        // Fix common broken UTF-8 sequences (mojibake)
        $description = str_replace(
            ["\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6", "\xc2\xa0"],
            ["'", "'", '"', '"', '-', '--', '...', ' '],
            $description
        );

        // Unicode Normalization (Form C)
        if (class_exists('\Normalizer')) {
            $description = \Normalizer::normalize($description, \Normalizer::FORM_C);
        }

        // Remove non-printable control characters while keeping whitespace
        $description = preg_replace('/[^\PC\s]/u', '', $description);

        $description = trim($description);

        // [DEBUG] Encoding check
        $detected = mb_detect_encoding($description, ['UTF-8', 'ISO-8859-1'], true);
        
        dump([
            'encoding' => $detected,
            'preview_end' => substr($description, -100),
        ]);

        return $description;
    }

    /**
     * Log success details.
     */
    private function logSuccess($description, $source)
    {
        $len = strlen($description);
        $preview = substr($description, 0, 200);
        Log::info("[SCRAPE] SUCCESS! Source: {$source}");
        Log::info("[SCRAPE] Description Length: {$len}");
        Log::info("[SCRAPE] Preview: {$preview}...");
    }
}
