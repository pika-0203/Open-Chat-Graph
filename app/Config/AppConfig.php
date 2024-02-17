<?php

namespace App\Config;

class AppConfig
{
    const SITE_ICON_FILE_PATH = 'assets/icon-192x192.png';
    const DEFAULT_OGP_IMAGE_FILE_PATH = 'assets/ogp.png';

    const GTAG_ID = 'G-DBS3CW3XH5';

    const DEVICE_COOKIE_EXPIRES = 60 * 60 * 24 * 30;

    const LINE_URL = 'https://line.me/ti/g2/';
    const LINE_IMG_PREVIEW_SUFFIX = '_p';

    const OPEN_CHAT_LIST_LIMIT = 100;
    const MY_LIST_LIMIT = 50;

    const CRON_EXECUTE_COUNT = 500;

    const CRON_START_MINUTE = 30;
    const CRON_MERGER_HOUR_RANGE_START = 23;
    const CRON_MERGER_HOUR_RANGE_END = 0;

    const LINE_OPEN_URL = 'https://openchat.line.me/jp/cover/';

    const SITEMAP_DIR = __DIR__ . '/../../public/sitemap.xml';

    const TOP_RANKING_INFO_FILE_PATH = __DIR__ . '/../../storage/static_data_top/ranking_info.dat';
    const TOP_RANKING_HOUR_INFO_FILE_PATH = __DIR__ . '/../../storage/static_data_top/ranking_hour_info.dat';
    const ROOT_PATH = __DIR__ . '/../../';

    const OPEN_CHAT_CATEGORY = [
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
    ];

    const DEFAULT_OPENCHAT_IMG_URL = [
        '1' => '0h6tJf0hQsaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOA',
        '2' => '0h6tJf8QWOaVt3H0eLAsEWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOQ',
        '3' => '0h6tJfJfGJaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPg',
        '4' => '0h6tJfahRYaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPQ',
        '5' => '0h6tJfzfJQaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPw',
    ];

    const OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH = __DIR__ . '/../../storage/cron_state/open_chat_api_db_merger_kill_flag.dat';
    const OPEN_CHAT_API_CRAWLING_KILL_FLAG_PATH = __DIR__ . '/../../storage/cron_state/open_chat_crawling_kill_flag.dat';

    const OPEN_CHAT_SUB_CATEGORIES_FILE_PATH = __DIR__ . '/../../storage/open_chat_sub_categories/subcategories.json';

    const OPEN_CHAT_RANKING_POSITION_DIR = __DIR__ . '/../../storage/ranking_position/ranking';
    const OPEN_CHAT_RISING_POSITION_DIR = __DIR__ . '/../../storage/ranking_position/rising';
    const OPEN_CHAT_HOUR_FILTER_ID_DIR = __DIR__ . '/../../storage/ranking_position/filter.dat';

    const OPEN_CHAT_ID_DATA_FILE_PATH = __DIR__ . '/../../storage/OpenChatBackupData';
}
