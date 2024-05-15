<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Services\Recommend\RecommendPageList;
use App\Services\Recommend\RecommendUtility;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;

class RecommendOpenChatPageController
{
    function __construct(
        private PageBreadcrumbsListSchema $breadcrumbsShema
    ) {
    }

    const Redirect = [
        'ChatGPT' => '生成AI・ChatGPT',
        'AI画像・イラスト生成' => '画像生成AI・AIイラスト',
        'Produce 101 Japan' => 'PRODUCE 101 JAPAN THE GIRLS（日プ女子）',
        'なりきり（全也）' => 'なりきり',
        'クーポン・お得情報' => 'クーポン・無料配布',
        'ロック' => '邦ロック',
    ];

    function index(
        RecommendPageList $recommendPageList,
        StaticDataFile $staticDataGeneration,
        string $tag
    ) {
        if (isset(self::Redirect[$tag])) return redirect('recommend?tag=' . urlencode(self::Redirect[$tag]), 301);
        if (!$recommendPageList->isValidTag($tag)) return false;

        $_dto = $staticDataGeneration->getRecommendPageDto();

        $count = 0;
        $extractTag = RecommendUtility::extractTag($tag);
        $pageDesc =
            "2019年のサービス開始以来、累計2200万人以上のユーザーに利用されているLINEオープンチャット。そこで、オプチャグラフでは、「{$tag}」をテーマにした中で、最近人数が急増しているルームのランキングを作成しました。このランキングは1時間ごとに更新され、新しいルームが継続的に追加されます。";

        $_meta = meta()
            ->setDescription($pageDesc)
            ->setOgpDescription($pageDesc);

        $_css = ['room_list', 'site_header', 'site_footer', 'recommend_page'];

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema(
            'おすすめ',
            'recommend',
            $extractTag,
            'recommend/?tag=' . urlencode($tag),
            true
        );

        $canonical = url('recommend?tag=' . urlencode($tag));

        $time = OpenChatServicesUtility::getModifiedCronTime($_dto->rankingUpdatedAt->format('H:i'))
            ->format('G:i');

        $rankingDto = $staticDataGeneration->getTopPageData();
        $rankingDto->hourlyList = array_slice($rankingDto->hourlyList, 0, 5);
        $rankingDto->dailyList = array_slice($rankingDto->dailyList, 0, 5);

        $recommendArray = $recommendPageList->getListDto($tag);
        if (!$recommendArray) {
            $_schema = '';
            $_meta->setTitle("【最新】「{$tag}」おすすめオープンチャットランキング");
            return view('recommend_content', compact(
                '_meta',
                '_css',
                '_breadcrumbsShema',
                'tag',
                'extractTag',
                'count',
                '_schema',
                '_dto',
                'rankingDto',
                'canonical',
                'time',
            ));
        }

        cacheControl(600);

        [$recommend, $diffMember] = $recommendArray;
        $recommendList = $recommend->getList(false);

        $count = $recommend->getCount();
        $headline = "【最新】「{$tag}」おすすめオープンチャットランキングTOP{$count}";
        $_meta->setTitle($headline);
        $_meta->setImageUrl(imgUrl($recommendList[0]['id'], $recommendList[0]['img_url']));
        $_meta->thumbnail = imgPreviewUrl($recommendList[0]['id'], $recommendList[0]['img_url']);

        $_schema = $this->breadcrumbsShema->generateRecommend(
            $headline,
            $_meta->description,
            url("recommend?tag=" . urlencode($tag)),
            new \DateTime('2024-04-06 08:00:00'),
            $_dto->rankingUpdatedAt,
            $tag,
            $recommendList
        );

        $tags = $recommendPageList->getFilterdTags($recommendList, $tag);

        return view('recommend_content', compact(
            '_meta',
            '_css',
            '_breadcrumbsShema',
            'recommend',
            'tag',
            'extractTag',
            'count',
            '_schema',
            '_dto',
            'rankingDto',
            'canonical',
            'tags',
            'time',
        ));
    }
}
