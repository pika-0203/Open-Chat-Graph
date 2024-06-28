<?php

declare(strict_types=1);

use App\Services\Accreditation\Recommend\StaticData\AccreditationStaticDataGenerator;
use PHPUnit\Framework\TestCase;

class AccreditationStaticDataGeneratorTest extends TestCase
{
    private AccreditationStaticDataGenerator $inst;

    public function test()
    {
        $this->inst = app(AccreditationStaticDataGenerator::class);
        debug($this->inst->updateStaticData());
        $this->assertTrue(true);
    }
}
