<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

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
        StaticDataFile $staticDataFile,
        RecommendPageList $recommendPageList,
        string $tag
    ) {
        if (isset(self::Redirect[$tag])) return redirect('recommend?tag=' . urlencode(self::Redirect[$tag]), 301);

        $_updatedAt = new \DateTime($staticDataFile->getRankingArgDto()->hourlyUpdatedAt);
        $updatedAtDate = new \DateTime($staticDataFile->getRankingArgDto()->rankingUpdatedAt);

        $count = 0;
        $pageTitle = "「{$tag}」オープンチャット人気ランキング【毎時更新】";
        $word = RecommendUtility::extractTag($tag);
        $pageDesc =
            "2019年のサービス開始以来、累計2200万人以上のユーザーに利用されているLINEオープンチャット。「{$tag}」に関するルームは多くのユーザーによって開設されています。";

        $_meta = meta()
            ->setTitle($pageTitle)
            ->setDescription($pageDesc)
            ->setOgpDescription($pageDesc);

        $_css = ['room_list', 'site_header', 'site_footer', 'recommend_page'];

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema(
            'おすすめ',
            'recommend',
            $tag,
            'recommend?tag=' . urlencode($tag),
            true
        );

        $canonical = url('recommend?tag=' . urlencode($tag));
        $recommendArray = $recommendPageList->getListDto($tag);

        if (!$recommendArray && $recommendPageList->isValidTag($tag)) {
            $_schema = '';
            return view('recommend_content', compact(
                '_meta',
                '_css',
                '_breadcrumbsShema',
                'tag',
                'count',
                '_schema',
                '_updatedAt',
                'canonical',
            ));
        } elseif (!$recommendArray) {
            return false;
        }

        [$recommend, $diffMember] = $recommendArray;
        $recommendList = $recommend->getList(false);

        $count = $recommend->getCount();
        $headline = "【{$tag}】オープンチャット人気ランキングTOP{$count}【毎時更新】";
        $_meta->title = "{$headline} | オプチャグラフ";
        $_meta->setImageUrl(imgUrl($recommendList[0]['id'], $recommendList[0]['img_url']));

        $_schema = $this->breadcrumbsShema->generateRecommend(
            $headline,
            $_meta->description,
            url("recommend?tag=" . urlencode($tag)),
            new \DateTime('2024-04-06 08:00:00'),
            $_updatedAt,
            $tag,
            $recommendList
        );

        $tags = $recommendPageList->getFilterdTags($recommendList, $tag);
        cache();

        return view('recommend_content', compact(
            '_meta',
            '_css',
            '_breadcrumbsShema',
            'recommend',
            'tag',
            'count',
            '_schema',
            '_updatedAt',
            'canonical',
            'tags',
            //'diffMember'
        ));
    }
}
