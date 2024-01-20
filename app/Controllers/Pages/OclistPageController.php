<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\ApiRepositories\OpenChatStatsRankingApiRepository;
use App\Models\ApiRepositories\OpenChatApiArgs;
use App\Models\ApiRepositories\OpenChatStatsRankingApiRepositoryWithGce;
use Shared\Exceptions\BadRequestException as HTTP400;
use Shadow\Kernel\Reception as Recp;
use Shadow\Kernel\Validator as Valid;

class OclistPageController
{
    protected const LIMIT = 40;

    function __construct(
        private OpenChatApiArgs $args
    ) {
        $this->setHeaders();
        $this->validateInputs();
    }

    private function setHeaders()
    {
        header('Access-Control-Allow-Origin: *');
    }

    private function validateInputs()
    {
        $error = HTTP400::class;
        Recp::$isJson = true;

        $this->args->page = Valid::num(Recp::input('page', 0), min: 0, e: $error);
        $this->args->limit = Valid::num(Recp::input('limit', self::LIMIT), min: 1, e: $error);
        $this->args->category = (int)Valid::str(Recp::input('category', '0'), regex: AppConfig::OPEN_CHAT_CATEGORY, e: $error);

        $this->args->list = Valid::str(Recp::input('list', 'daily'), regex: ['daily', 'weekly', 'all'], e: $error);
        $this->args->order = Valid::str(Recp::input('order', 'asc'), regex: ['asc', 'desc'], e: $error);
        $this->args->sort = Valid::str(Recp::input('sort', 'rank'), regex: ['rank', 'increase', 'rate', 'member', 'created_at'], e: $error);

        $this->args->keyword = Valid::str(Recp::input('keyword', ''), emptyAble: true, maxLen: 1000, e: $error);
        $this->args->sub_category = Valid::str(Recp::input('sub_category', ''), emptyAble: true, maxLen: 40, e: $error);
    }

    function index()
    {
        switch ($this->args->list) {
            case 'daily':
                if ($this->args->keyword) {
                    return response(app(OpenChatStatsRankingApiRepositoryWithGce::class)->findDailyStatsRanking($this->args));
                }

                return response(app(OpenChatStatsRankingApiRepository::class)->findDailyStatsRanking($this->args));
            case 'weekly':
                if ($this->args->keyword) {
                    return response(app(OpenChatStatsRankingApiRepositoryWithGce::class)->findWeeklyStatsRanking($this->args));
                }

                return response(app(OpenChatStatsRankingApiRepository::class)->findWeeklyStatsRanking($this->args));
            case 'all':
                if ($this->args->keyword) {
                    return response(app(OpenChatStatsRankingApiRepositoryWithGce::class)->findStatsAll($this->args));
                }

                return response(app(OpenChatStatsRankingApiRepository::class)->findStatsAll($this->args));
        }
    }
}
