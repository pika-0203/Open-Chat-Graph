<?php

declare(strict_types=1);

namespace App\Services\Bot;

/**
 * Class BotFightUserAgents
 *
 * This class fetches and tests user agent strings from a JSON file.
 */
class BotFightUserAgents
{
    /**
     * URL of the JSON file containing crawler user agent strings.
     */
    private const JSON_URL = 'https://raw.githubusercontent.com/monperrus/crawler-user-agents/master/crawler-user-agents.json';

    /**
     * Local file to store the JSON data.
     */
    private const REGEX_FILE = __DIR__ . '/../../../storage/bot/crawler-user-agents-regex.txt';

    private string $regex;

    /**
     * CrawlerUserAgents constructor.
     *
     * @throws \RuntimeException If there is a problem fetching the JSON or writing to disk.
     */
    public function __construct()
    {
        try {
            $regex = file_get_contents(self::REGEX_FILE);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to read: ' . $e->getMessage());
        }

        if (!$regex)
            throw new \RuntimeException('text is empty');

        $this->regex = $regex;
    }

    /**
     * Tests if patterns work in PHP's preg_match.
     *
     * @throws \RuntimeException If there is a problem with the pattern.
     */
    public function isCrawler(string $ua): bool
    {
        try {
            return (bool)preg_match('/' . $this->regex . '/', $ua);
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

        $decoded = json_decode($json, true);
        if (!is_array($decoded) || !isset($decoded[0]['pattern'])) {
            throw new \RuntimeException('Invalid JSON: ' . self::JSON_URL);
        }

        $patterns = array_map(fn ($entry) => $entry['pattern'], $decoded);
        $regexp = implode('|', $patterns);

        try {
            file_put_contents(self::REGEX_FILE, $regexp);
        } catch (\ErrorException $e) {
            throw new \RuntimeException('Failed to write JSON: ' . $e->getMessage());
        }
    }
}
