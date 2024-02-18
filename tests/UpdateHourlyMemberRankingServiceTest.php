<?php

declare(strict_types=1);

use App\Services\UpdateHourlyMemberRankingService;
use App\Services\UpdateRankingService;
use PHPUnit\Framework\TestCase;

class UpdateHourlyMemberRankingServiceTest extends TestCase
{
    private UpdateHourlyMemberRankingService $inst;
    private UpdateRankingService $inst2;

    public function test()
    {
        $this->inst2 = app(UpdateRankingService::class);
        $this->inst = app(UpdateHourlyMemberRankingService::class);

        $this->inst->update();

        $this->assertTrue(true);
    } 
}
