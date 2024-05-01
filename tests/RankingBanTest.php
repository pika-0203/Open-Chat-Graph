<?php

declare(strict_types=1);

use App\Services\RankingBan\RankingBan;
use PHPUnit\Framework\TestCase;

class RankingBanTest extends TestCase
{
    private RankingBan $inst;
    public function test()
    {
        $this->inst = app(RankingBan::class);

        $this->inst->updateRankingBanTable();

        $this->assertTrue(true);
    }
}
