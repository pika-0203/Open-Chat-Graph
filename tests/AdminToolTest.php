<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class AdminToolTest extends TestCase
{
    public function test()
    {
        debug(getUnserializedFile('1sz3j4GUhYQtest.dat'));

        $this->assertTrue(true);
    }
}
