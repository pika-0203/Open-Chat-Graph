<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Class CrawlerUserAgents
 *
 * This class fetches and tests user agent strings from a JSON file.
 */
//class CrawlerUserAgents
{
    /**
     * URL of the JSON file containing crawler user agent strings.
     */
    private const JSON_URL = 'https://raw.githubusercontent.com/monperrus/crawler-user-agents/master/crawler-user-agents.json';

    /**
     * Local file to store the JSON data.
     */
    private const JSON_FILE = __DIR__ . '/../../storage/crawler-user-agents.json';

    private array $data;

    /**
     * CrawlerUserAgents constructor.
     *
     * @throws \RuntimeException If there is a problem fetching the JSON or writing to disk.
     */
    public function __construct()
    {
        try {
            $this->data = json_decode(file_get_contents(self::JSON_FILE), true);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to read JSON: ' . $e->getMessage());
        }

        if (!is_array($this->data) || !isset($this->data[0]['pattern'])) {
            throw new \RuntimeException('Invalid JSON: ' . self::JSON_FILE);
        }
    }

    /**
     * Tests if patterns work in PHP's preg_match.
     *
     * @throws \RuntimeException If there is a problem with the pattern.
     */
    public function isCrawler(string $ua): bool
    {
        $patterns = array_map(fn ($entry) => $entry['pattern'], $this->data);
        $regexp = implode('|', $patterns);
        try {
            return (bool)preg_match('/' . $regexp . '/', $ua);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('There is a problem with the pattern: ' . $e->getMessage());
        }
    }

    /**
     * Fetches crawler user agent strings from JSON and stores them locally.
     *
     * @throws \RuntimeException If there is a problem fetching the JSON or writing to disk.
     */
    static function fetchCrawlerUserAgentsJson(): void
    {
        try {
            $json = file_get_contents(self::JSON_URL);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to fetch JSON: ' . $e->getMessage());
            return;
        }

        try {
            file_put_contents(self::JSON_FILE, $json);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to write JSON: ' . $e->getMessage());
        }
    }
}
