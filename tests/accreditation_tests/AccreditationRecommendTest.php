<?php

declare(strict_types=1);

use App\Services\Accreditation\Recommend\AcrreditationRecommend;
use App\Services\Accreditation\Recommend\StaticData\AccreditationStaticDataGenerator;
use PHPUnit\Framework\TestCase;

class AccreditationRecommendTest extends TestCase
{
    private AcrreditationRecommend $inst;

    public function test()
    {
        $this->inst = app(AcrreditationRecommend::class);
        debug($this->inst->getRandomQuestions(3));
        $this->assertTrue(true);
    }
}
