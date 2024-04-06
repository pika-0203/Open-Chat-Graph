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
        "宣伝",
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
    ];

    function index(
        RecommendRankingBuilder $recommendRankingBuilder,
        RecommendPageRepository $recommendPageRepository,
        StaticDataFile $staticDataFile,
        string $tag
    ) {
        $_updatedAt = new \DateTime($staticDataFile->getRankingArgDto()->hourlyUpdatedAt);
        $count = 0;
        $pageTitle = "「{$tag}」関連のおすすめ人気オプチャ【最新】";

        $_meta = meta()
            ->setTitle($pageTitle, false)
            ->setDescription(
                "LINEオープンチャットにて特に人気のルームから、「{$tag}」にマッチするルームを毎時更新でご紹介！気になるルームを見つけたら気軽に参加してみましょう！"
            );

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
            header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
            header('Retry-After: ' . (10 * 60));

            /** @var RecommendUpdater $recommendUpdater */
            $recommendUpdater = app(RecommendUpdater::class);
            $tags = $recommendUpdater->getAllTagNames();
            $result = in_array($tag, $tags);
            $_schema = $this->schema($_meta, $_updatedAt, $tag);
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
        $count = $recommend->getCount();
        $_meta->title = "「{$tag}」関連のおすすめ人気オプチャ{$count}選【最新】";
        $_schema = $this->schema($_meta, $_updatedAt, $tag);

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

    private function schema($_meta, $_updatedAt, $tag)
    {
        return $this->breadcrumbsShema->generateStructuredDataWebPage(
            $_meta->title,
            $_meta->description,
            url("recommend/" . urlencode($tag)),
            url('assets/ogp.png'),
            'pika-0203',
            'https://github.com/pika-0203',
            'https://avatars.githubusercontent.com/u/132340402?v=4',
            'オプチャグラフ',
            url('assets/icon-192x192.png'),
            new \DateTime('2024-04-06 08:00:00'),
            $_updatedAt,
        );
    }
}
