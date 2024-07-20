<?php

namespace Tests\Unit\Services\OpenChat\Utility;

use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatRejectUtility;
use PHPUnit\Framework\TestCase;

class OpenChatRejectUtilityTest extends TestCase
{
    private OpenChatRepositoryInterface $openChatRepository;
    private OpenChatRejectUtility $openChatRejectUtility;

    protected function setUp(): void
    {
        $this->openChatRepository = app(OpenChatRepositoryInterface::class);
        $this->openChatRejectUtility = new OpenChatRejectUtility($this->openChatRepository);
    }

    public function testIsRejectedOpenChatReturnsTrueWhenEmidIsRejected(): void
    {
        $dto = new OpenChatDto();
        $dto->emid = 'BhTEaHzCCA895ZqqG8lOL-fzFoLp3Ymw3N6rsfq3e90Lh5FSr7pviwk5Ozk';

        $result = $this->openChatRejectUtility->isRejectedOpenChat($dto);

        $this->assertTrue($result);
    }
}