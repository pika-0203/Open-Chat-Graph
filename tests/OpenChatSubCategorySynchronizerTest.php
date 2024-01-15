<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use App\Services\OpenChat\SubCategory\OpenChatSubCategorySynchronizer;

class OpenChatSubCategorySynchronizerTest extends TestCase
{
    public function test()
    {
        /**
         * @var OpenChatSubCategorySynchronizer $test
         */
        $test = app(OpenChatSubCategorySynchronizer::class);
        $test->syncSubCategoriesAll();

        $this->assertTrue(true);
    }
}
