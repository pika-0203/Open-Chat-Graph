<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\RecommendRepositories\RecommendPageRepository;
use App\Services\Recommend\Enum\RecommendListType;
use App\Services\Recommend\RecommendRankingBuilder;
use App\Services\Recommend\RecommendUpdater;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;

class RecommendOpenChatPageController
{
    function __construct(
        private PageBreadcrumbsListSchema $breadcrumbsShema
    ) {
    }

    const TagFilter = [
        'スマホ',
        '営業',
        '大人',
        'スタンプ',
        'SNS',
        'Instagram（インスタ）',
        '知的財産',
        "東京",
        "北海道",
        "神奈川",
        "愛知",
        "京都",
        "大阪",
        "兵庫",
        "福岡",
        "関東",
        "関西",
        "九州",
        "沖縄",
        "即承認",
        "海外",
        "全国 雑談",
        "70代",
        "60代",
        "50代",
        "加工",
        "フェス",
        "自衛隊",
        "レスバ",
        "unistyle",
        "jobhunt",
        "邦画",
    ];

    function index(
        RecommendRankingBuilder $recommendRankingBuilder,
        RecommendPageRepository $recommendPageRepository,
        StaticDataFile $staticDataFile,
        string $tag
    ) {
        $_updatedAt = new \DateTime($staticDataFile->getRankingArgDto()->hourlyUpdatedAt);
        $updatedAtDate = new \DateTime($staticDataFile->getRankingArgDto()->rankingUpdatedAt);
        $count = 0;
        $pageTitle = "「{$tag}」関連のおすすめ人気オプチャ【最新】";

        $pageDesc = "LINEオープンチャットでいま人気のルームから、「{$tag}」に関する厳選ルームをご紹介！気になるルームを見つけたら気軽に参加してみましょう！";
        $_meta = meta()
            ->setTitle($pageTitle, false)
            ->setDescription($pageDesc)
            ->setOgpDescription($pageDesc);

        $_css = ['room_list', 'site_header', 'site_footer', 'recommend_page'];
        $_breadcrumbsShema = $this->breadcrumbsShema
            ->generateSchema('おすすめ', 'recommend', $tag, 'recommend?tag=' . urlencode($tag), true);

        $canonical = url('recommend?tag=' . urlencode($tag));

        $recommend = $recommendRankingBuilder->getRanking(
            RecommendListType::Tag,
            0,
            $tag,
            $tag,
            $recommendPageRepository
        );

        if (!$recommend) {
            /** @var RecommendUpdater $recommendUpdater */
            $recommendUpdater = app(RecommendUpdater::class);
            $tags = $recommendUpdater->getAllTagNames();
            $result = in_array($tag, $tags);
            $_schema = '';
            return $result ? view('recommend_content', compact(
                '_meta',
                '_css',
                '_breadcrumbsShema',
                'tag',
                'count',
                '_schema',
                '_updatedAt',
                'canonical',
            )) : false;
        }

        cache();

        $recommendList = $recommend->getList(false);

        $tags = sortAndUniqueArray(
            array_merge(
                array_column($recommendList, 'tag1'),
                array_column($recommendList, 'tag2')
            )
        );
        $tags = array_filter($tags, fn ($e) => !(in_array($e, self::TagFilter) || $e === $tag));

        $tagCategory = sortAndUniqueArray(array_column($recommendList, 'category'));

        $count = $recommend->getCount();
        $_meta->title = "「{$tag}」関連のおすすめ人気オプチャ{$count}選【最新】";
        $_meta->setImageUrl(imgUrl($recommendList[0]['id'], $recommendList[0]['img_url']));

        $_schema = $this->breadcrumbsShema->generateRecommend(
            $_meta->title,
            $_meta->description,
            url("recommend?tag=" . urlencode($tag)),
            new \DateTime('2024-04-06 08:00:00'),
            $_updatedAt,
            $tag,
            $recommendList
        );

        $_meta = $_meta->generateTags(true);

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
            'tags'
        ));
    }
}
