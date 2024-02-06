<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\AppConfig;
use App\Models\ApiRepositories\OpenChatStatsRankingApiRepository;
use App\Models\ApiRepositories\OpenChatApiArgs;
use Shared\Exceptions\BadRequestException as HTTP400;
use Shadow\Kernel\Reception as Recp;
use Shadow\Kernel\Validator as Valid;

class OpenChatRankingPageApiController
{
    function __construct(
        private OpenChatApiArgs $args
    ) {
        localCORS();
        $this->validateInputs();
    }

    private function validateInputs()
    {
        $error = HTTP400::class;
        Recp::$isJson = true;

        $this->args->page = Valid::num(Recp::input('page', 0), min: 0, e: $error);
        $this->args->limit = Valid::num(Recp::input('limit'), min: 1, e: $error);
        $this->args->category = (int)Valid::str(Recp::input('category', '0'), regex: AppConfig::OPEN_CHAT_CATEGORY, e: $error);

        $this->args->list = Valid::str(Recp::input('list', 'daily'), regex: ['daily', 'weekly', 'all'], e: $error);
        $this->args->order = Valid::str(Recp::input('order', 'asc'), regex: ['asc', 'desc'], e: $error);
        $this->args->sort = Valid::str(Recp::input('sort', 'rank'), regex: ['rank', 'increase', 'rate', 'member', 'created_at'], e: $error);

        $this->args->keyword = Valid::str(Recp::input('keyword', ''), emptyAble: true, maxLen: 1000, e: $error);
        $this->args->sub_category = Valid::str(Recp::input('sub_category', ''), emptyAble: true, maxLen: 40, e: $error);
    }

    function index(OpenChatStatsRankingApiRepository $repo)
    {
        switch ($this->args->list) {
            case 'daily':
                return response($repo->findDailyStatsRanking($this->args));
            case 'weekly':
                return response($repo->findWeeklyStatsRanking($this->args));
            case 'all':
                return response($repo->findStatsAll($this->args));
        }
    }
}
