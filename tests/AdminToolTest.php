<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Services\Admin\AdminTool;

class AdminToolTest extends TestCase
{
    public function test()
    {
        
        debug(AdminTool::sendLineNofity('test'));

        $this->assertTrue(true);
    }
}
