<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\OpenChat\Cron;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\StatisticsRankingUpdaterRepositoryInterface;
use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Config\AppConfig;
use App\Config\OpenChatCrawlerConfig;
use App\Services\OpenChat\AddOpenChat;
use App\Services\Sitemap;
use App\Models\Repositories\LogRepositoryInterface;

class CronApiController
{
    /**
     * オープンチャット更新のAPI(500件ずつ処理)
     */
    function index(
        Cron $statisticsCron,
        LogRepositoryInterface $logRepository,
    ) {
        // レスポンスを返して切断する
        response(['updateOpenChatCron' => 'executed'])->send();
        fastcgi_finish_request();

        try {
            $statisticsCron->handle();
        } catch (\Throwable $e) {
            $logRepository->logUpdateOpenChatError(0, 0, 'null', 'null', $e->getMessage());
        }

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
        $resultRowCount = $rankingUpdater->updateCreateRankingTable();

        // トップページのキャッシュファイルを生成する
        $rankingList = [];
        $rankingList['openChatList'] = $openChatRepository->findMemberStatsRanking(0, 10);
        $rankingList['updatedAt'] = time();

        foreach ($rankingList['openChatList'] as &$oc) {
            // 説明文を半角140文字以内にする
            $oc['description'] = mb_strimwidth($oc['description'], 0, 140, '…',);
        }

        saveArrayToFile(AppConfig::FILEPATH_TOP_RANKINGLIST, $rankingList);

        // サイトマップを更新する
        $sitemap->updateSitemap();

        return response(['rankingUpdaterResultCount' => $resultRowCount]);
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
}
