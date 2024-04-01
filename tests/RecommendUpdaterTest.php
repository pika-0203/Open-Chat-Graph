<?php

declare(strict_types=1);

use App\Services\Recommend\RecommendUpdater;
use PHPUnit\Framework\TestCase;

class RecommendUpdaterTest extends TestCase
{
    private RecommendUpdater $inst;

    public function test()
    {
        $this->inst = app(RecommendUpdater::class);

        $r = $this->inst->replace(['K也', ["K也_OR_🇰🇷 也_OR_𝐊 也_AND_ちんこ", "いぬ_OR_いす"]], 'name');

        debug($r);

        $this->assertTrue(true);
    }

    public function test2()
    {
        $this->inst = app(RecommendUpdater::class);

        $this->inst->updateRecommendTables(false);

        $this->assertTrue(true);
    }
}
