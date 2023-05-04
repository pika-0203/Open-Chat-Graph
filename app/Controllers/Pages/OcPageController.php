<?php

use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\Statistics\StatisticsService;

class OcPageController
{
    function index(
        OpenChatRepositoryInterface $openChatRepository,
        StatisticsService $statistics,
        int $open_chat_id
    ) {
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $statisticsData = $statistics->getStatisticsData($open_chat_id);

        $name = $oc['name'];
        $desc = "オープンチャット「{$name}」のメンバー数推移をグラフで表示します。人気度や活性度を視覚的にチェック出来ます！";
        $ogpDesc = 'グラフ化されたメンバー数推移から人気度や活性度を視覚的にチェック出来ます！';

        $_meta = meta()->setTitle($name)->setDescription($desc)->setOgpDescription($ogpDesc);
        $_css = ['room_page', 'site_header'];

        return view('statistics/header', compact('_meta', '_css'))
            ->make('statistics/oc_content', compact('oc') + $statisticsData)
            ->make('statistics/footer');
    }
}
