<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\OpenChat\Cron;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface;
use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Config\AppConfig;
use App\Config\OpenChatCrawlerConfig;
use App\Services\OpenChat\AddOpenChat;
use App\Services\Statistics\OpenChatStatisticsRanking;
use App\Services\Sitemap;

class CronApiController
{
    private LogRepositoryInterface $logRepository;

    function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * オープンチャット更新のAPI (500件ずつ処理)
     */
    function index(Cron $statisticsCron, UpdateOpenChatRepositoryInterface $updateRepository)
    {
        $this->setExceptionHandler();
        
        // レスポンスを返して切断する
        $idArray = $updateRepository->getUpdateTargetOpenChatId();
        response(['updateOpenChatCron' => count($idArray)])->send();
        fastcgi_finish_request();

        $statisticsCron->handle();
        exit;
    }

    /**
     * 本日更新対象のレコード数を返すAPI
     */
    function ocrowcount(UpdateOpenChatRepositoryInterface $updateRepository)
    {
        $idArray = $updateRepository->getUpdateTargetOpenChatId();
        return response(['openChatRowCount' => count($idArray)]);
    }

    /**
     * ランキング更新のAPI
     */
    function rank(
        StatisticsRankingUpdaterRepositoryInterface $rankingUpdater,
        OpenChatListRepositoryInterface $openChatRepository,
        Sitemap $sitemap
    ) {
        $resultRowCount = $rankingUpdater->updateCreateDailyRankingTable();
        $resultPastWeekRowCount = $rankingUpdater->updateCreatePastWeekRankingTable();

        // トップページのキャッシュファイルを生成する
        $rankingList = [];
        $rankingList['openChatList'] = $openChatRepository->findMemberStatsDailyRanking(0, 10);
        $rankingList['pastWeekOpenChatList'] = $openChatRepository->findMemberStatsPastWeekRanking(0, 10);
        $rankingList['updatedAt'] = time();

        // 説明文の文字数を詰める
        OpenChatStatisticsRanking::trimDescriptions($rankingList['openChatList']);
        OpenChatStatisticsRanking::trimDescriptions($rankingList['pastWeekOpenChatList']);

        saveArrayToFile(AppConfig::FILEPATH_TOP_RANKINGLIST, $rankingList);

        // サイトマップを更新する
        $sitemap->updateSitemap();

        return response(['rankingUpdaterResult' => [$resultRowCount, $resultPastWeekRowCount]]);
    }

    /**
     * オープンチャット追加のAPI(管理者用)
     */
    function addoc(AddOpenChat $openChat, string $url)
    {
        // URL文字列を検証
        $isValidUrl = fn ($url) => preg_match(OpenChatCrawlerConfig::LINE_URL_MATCH_PATTERN, $url);

        if (!$isValidUrl($url)) {
            // LINEのURLではない場合、リダイレクト先のURLを調べる
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);

            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            if (!$isValidUrl($url)) {
                // リダイレクト先も無効なURLの場合
                return response([
                    'message' => '無効なURLです。',
                    'id' => null
                ]);
            }
        }

        // オープンチャットを追加
        $result = $openChat->add($url);
        return response($result);
    }

    private function setExceptionHandler()
    {
        $handler = $this->exceptionHandler(...);

        set_exception_handler($handler);

        /**
         * Sets the error reporting level to include all errors.
         */
        error_reporting(E_ALL);

        /**
         * Registers a custom error handler that throws exceptions for all errors.
         */
        set_error_handler(function ($no, $msg, $file, $line) {
            if (error_reporting() !== 0) {
                throw new \ErrorException($msg, 0, $no, $file, $line);
            }
        });

        /**
         * Registers a shutdown function that checks for fatal errors and
         * passes them to ExceptionHandler::handleException().
         */
        register_shutdown_function(function () use ($handler) {
            $last = error_get_last();
            if (
                isset($last['type'])
                && boolval($last['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR))
            ) {
                $handler(
                    new \ErrorException($last['message'], 0, $last['type'], $last['file'], $last['line'])
                );
            }
        });
    }

    private function exceptionHandler(\Throwable $e)
    {
        $this->logRepository->logUpdateOpenChatError(0, 'null', 'null', $e->getMessage());
        exit;
    }
}