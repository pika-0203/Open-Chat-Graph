<?php

namespace App\Config;

use Shared\MimimalCmsConfig;

class AppConfig
{
    const RECOMMEND_LIST_LIMIT =    50;
    const MIN_MEMBER_DIFF_HOUR =    3;
    const MIN_MEMBER_DIFF_H24 =     8;
    const MIN_MEMBER_DIFF_WEEK =    10;

    const SITE_ICON_FILE_PATH = 'assets/icon-192x192.png';
    const DEFAULT_OGP_IMAGE_FILE_PATH = 'assets/ogp.png';

    const GTM_ID = 'GTM-NTK2GPTF';

    const ETAG_ARG = [300, 3600 * 24, false];

    const LINE_URL = 'https://line.me/ti/g2/';

    const LINE_APP_URL = 'line://ti/g2/';
    const LINE_APP_SUFFIX = '?utm_source=line-openchat-seo&utm_medium=category&utm_campaign=default';

    const OPENCHAT_IMG_PATH = [
        '' =>    'oc-img',
        '/tw' => 'oc-img-tw',
        '/th' => 'oc-img-th',
    ];

    const OPENCHAT_IMG_PREVIEW_PATH = 'preview';
    const OPENCHAT_IMG_PREVIEW_SUFFIX = '_p';

    const OPEN_CHAT_LIST_LIMIT =      100;
    const MY_LIST_LIMIT =             50;
    const RECENT_COMMENT_LIST_LIMIT = 50;
    const TOP_RANKING_LIST_LIMIT =    5;

    const CRON_START_MINUTE = 30;
    const CRON_MERGER_HOUR_RANGE_START = 23;
    const CRON_MERGER_HOUR_RANGE_END = 0;

    const DEFAULT_OPENCHAT_IMG_URL = [
        '0h6tJf0hQsaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOA',
        '0h6tJf8QWOaVt3H0eLAsEWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOQ',
        '0h6tJfJfGJaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPg',
        '0h6tJfahRYaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPQ',
        '0h6tJfzfJQaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPw',
    ];
    const DEFAULT_OPENCHAT_IMG_URL_HASH = [
        '2AtTNcODU67',
        '3SXDWf2OXqcY',
        '2pPOKy2ldZWl',
        '3y6jWliwCg1',
        '4DceVI1KwU1k',
    ];
    const ADD_OPEN_CHAT_DEFAULT_OPENCHAT_IMG_URL_HASH = '2AtTNcODU67';

    // TODO:多言語対応
    const LINE_OPEN_URL = 'https://openchat.line.me/jp/cover/';
    const LINE_OPEN_URL_SUFFIX = '?utm_source=line-openchat-seo&utm_medium=category&utm_campaign=default';

    const ROOT_PATH =   __DIR__ . '/../../';
    const SITEMAP_DIR = __DIR__ . '/../../public/sitemap.xml';

    const FURIGANA_CACHE_DIR = __DIR__ . '/../../storage/furigana';

    private const STORAGE_DIR = [
        '' =>    __DIR__ . '/../../storage/ja',
        '/tw' => __DIR__ . '/../../storage/tw',
        '/th' => __DIR__ . '/../../storage/th',
    ];
    const STORAGE_FILES = [
        'addCronLogDest' =>               '/logs/cron.log',
        'sqliteStatisticsDb' =>           '/SQLite/statistics/statistics.db',
        'sqliteRankingPositionDb' =>      '/SQLite/ranking_position/ranking_position.db',
        'openChatSubCategories' =>        '/open_chat_sub_categories/subcategories.json',
        'openChatSubCategoriesTag' =>     '/open_chat_sub_categories/subcategories_tag.json',
        'openChatRankingPositionDir' =>   '/ranking_position/ranking',
        'openChatRisingPositionDir' =>    '/ranking_position/rising',
        'openChatHourFilterId' =>         '/ranking_position/filter.dat',
        'dailyCronUpdatedAtDate' =>       '/static_data_top/daily_updated_at.dat',
        'hourlyCronUpdatedAtDatetime' =>  '/static_data_top/hourly_updated_at.dat',
        'hourlyRealUpdatedAtDatetime' =>  '/static_data_top/real_updated_at.dat',
        'commentUpdatedAtMicrotime' =>    '/static_data_top/comment_updated_at.dat',
        'tagUpdatedAtDatetime' =>         '/static_data_top/tag_updated_at.dat',
        'topPageRankingData' =>           '/static_data_top/ranking_list.dat',
        'rankingArgDto' =>                '/static_data_top/ranking_arg_dto.dat',
        'recommendPageDto' =>             '/static_data_top/recommend_page_dto.dat',
        'tagList' =>                      '/static_data_top/tag_list.dat',
        'recommendStaticDataDir' =>       '/static_data_recommend/tag',
        'categoryStaticDataDir' =>        '/static_data_recommend/category',
        'officialStaticDataDir' =>        '/static_data_recommend/official',
    ];

    /**
     * @param string $storageFileName
     */
    static function getStorageFilePath(string $storageFileName): string
    {
        return self::STORAGE_DIR[MimimalCmsConfig::$urlRoot] . self::STORAGE_FILES[$storageFileName];
    }

    const DB_NAME = [
        '' =>    'cf782105_ocreview',
        '/tw' => 'cf782105_ocreviewtw',
        '/th' => 'cf782105_ocreviewth',
    ];
    const OpenChatTable =    'open_chat';
    const RankingHourTable = 'statistics_ranking_hour';
    const RankingDayTable =  'statistics_ranking_hour24';
    const RankingWeekTable = 'statistics_ranking_week';

    const RANKING_POSITION_DB_NAME = [
        '' =>    'cf782105_ranking',
        '/tw' => 'cf782105_rankingtw',
        '/th' => 'cf782105_rankingth',
    ];
    // TODO:多言語対応
    const USER_LOG_DB_NAME = [
        '' =>    'cf782105_userlog',
        '/tw' => 'cf782105_userlog',
        '/th' => 'cf782105_userlog',
    ];
    // TODO:多言語対応
    const COMMENT_DB_NAME = [
        '' =>    'cf782105_comment',
        '/tw' => 'cf782105_comment',
        '/th' => 'cf782105_comment',
    ];

    const OPEN_CHAT_CATEGORY = [
        '' => [
            'ゲーム' => 17,
            'スポーツ' => 16,
            '芸能人・有名人' => 26,
            '同世代' => 7,
            'アニメ・漫画' => 22,
            '金融・ビジネス' => 40,
            '音楽' => 33,
            '地域・暮らし' => 8,
            'ファッション・美容' => 20,
            'イラスト' => 41,
            '研究・学習' => 11,
            '働き方・仕事' => 5,
            '学校・同窓会' => 2,
            '料理・グルメ' => 12,
            '健康' => 23,
            '団体' => 6,
            '妊活・子育て' => 28,
            '乗り物' => 19,
            '写真' => 37,
            '旅行' => 18,
            '動物・ペット' => 27,
            'TV・VOD' => 24,
            '本' => 29,
            '映画・舞台' => 30,
            'すべて' => 0,
        ],
        '/tw' => [
            '流行／美妝' => 20,
            '金融／商業' => 40,
            '美食' => 12,
            '團體／組織' => 6,
            '旅遊' => 18,
            '娛樂' => 42,
            '家庭／親子' => 4,
            '工作' => 44,
            '學習' => 11,
            '遊戲' => 17,
            '興趣' => 14,
            '運動／健身' => 16,
            '寵物' => 27,
            '公司／企業' => 5,
            '心情' => 43,
            '科技' => 34,
            '健康' => 23,
            '學校／校友' => 2,
            '動畫／漫畫' => 22,
            '其他' => 35,
            '全部' => 0,
        ],
        '/th' => [
            'แฟนคลับ' => 10,
            'การศึกษา' => 11,
            'การเงิน & ธุรกิจ' => 40,
            'งานอดิเรก' => 14,
            'ท่องเที่ยว' => 18,
            'เทคโนโลยี' => 34,
            'เกม' => 17,
            'ท้องถิ่น' => 8,
            'สัตว์เลี้ยง' => 27,
            'รถยนต์' => 19,
            'เพลง' => 33,
            'แฟชั่น & บิวตี้' => 20,
            'กีฬา' => 16,
            'อาหาร' => 12,
            'อนิเมะ & การ์ตูน' => 22,
            'ภาพยนตร์' => 30,
            'โรงเรียน' => 2,
            'การถ่ายภาพ' => 37,
            'รายการทีวี' => 24,
            'เด็ก' => 28,
            'อื่นๆ' => 35,
            'ทั้งหมด' => 0,
        ]
    ];

    static bool $isDevlopment = false;

    /** @var array<string, int> */
    static array $developmentEnvUpdateLimit = [
        'OpenChatImageUpdater' => 10,
        'OpenChatHourlyInvitationTicketUpdater' => 10,
        'DailyUpdateCronService' => 10,
        'RankingBanTableUpdater' => 10,
    ];
}
