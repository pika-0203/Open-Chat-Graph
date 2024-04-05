<?php

declare(strict_types=1);

use App\Services\Recommend\RecommendUpdater;
use PHPUnit\Framework\TestCase;

class RecommendUpdaterTest extends TestCase
{
    private RecommendUpdater $inst;

    public function test2()
    {
        $this->inst = app(RecommendUpdater::class);

        $this->inst->updateRecommendTables(false);

        $this->assertTrue(true);
    }
}
