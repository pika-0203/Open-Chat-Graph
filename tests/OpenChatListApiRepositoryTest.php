<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Models\ApiRepositories\OpenChatListApiRepository;

class OpenChatListApiRepositoryTest extends TestCase
{
    public function test()
    {
        /**
         * @var OpenChatListApiRepository $repo
         */
        $repo = app(OpenChatListApiRepository::class);
        $result = $repo->findStatsRanking(0, -1);

        debug(json_encode($result, JSON_UNESCAPED_UNICODE));

        $this->assertTrue(true);
    }
}
