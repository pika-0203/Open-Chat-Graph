<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Review;

use PHPUnit\Framework\TestCase;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;

class UpdateOpenChatRepositoryTest extends TestCase
{
    public function testgetOpenChatDataById(): void
    {
        /**
         * @var UpdateOpenChatRepositoryInterface $Service
         */
        $Service = app()->make(UpdateOpenChatRepositoryInterface::class);
        $result = $Service->getOpenChatDataById(1);
        var_dump($result);
        $this->assertIsBool(true);
    }

    public function testupdateOpenChat()
    {
        $dto = new OpenChatUpdaterDto();
        $dto->category = 6;
        $dto->name = 'オプチャ ライブトークの部屋';

        /**
         * @var UpdateOpenChatRepositoryInterface $Service
         */
        $Service = app()->make(UpdateOpenChatRepositoryInterface::class);

        $Service->updateOpenChat(1, dto: $dto);

        $this->assertIsBool(true);
    }
}
