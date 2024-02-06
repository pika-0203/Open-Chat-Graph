<?php

declare(strict_types=1);

namespace Tests\Services\OpenChat\Crawler;

use App\Services\OpenChat\Crawler\OpenChatCrawler;
use PHPUnit\Framework\TestCase;

class OpenChatCrawlerTest extends TestCase
{
    private OpenChatCrawler $openChatCrawler;
    private string $invitationTicket;

    protected function setUp(): void
    {
        $this->openChatCrawler = app(OpenChatCrawler::class);
        $this->invitationTicket = 'MMYUF3C8P0';
    }

    public function testGetOpenChat(): void
    {
        $result = $this->openChatCrawler->fetchOpenChatDto($this->invitationTicket);

        debug($result);

        $this->assertTrue($result !== false);
    }
}
