<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class AdminToolTest extends TestCase
{
    public function test()
    {
        debug(getUnserializedFile('2DBvvawGpYhtest1.dat'));

        $this->assertTrue(true);
    }
}
