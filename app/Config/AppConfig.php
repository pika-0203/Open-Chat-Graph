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
    const OPENCHAT_IMG_PATH = '/oc-img/';
    const OPENCHAT_IMG_PREVIEW_PATH = '/preview/';

    const OPEN_CHAT_LIST_LIMIT = 100;
    const MY_LIST_LIMIT = 20;

    const CRON_EXECUTE_COUNT = 500;

    const LINE_OPEN_URL = 'https://line.me/ti/g2/';

    const SITEMAP_DIR = __DIR__ . '/../../public/sitemap.xml';

    const TOP_RANKING_INFO_FILE_PATH = __DIR__ . '/../../storage/static_data_top/ranking_info.dat';

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

    const OPEN_CHAT_CATEGORY_KEYS = [
        17 => 'ゲーム',
        16 => 'スポーツ',
        26 => '芸能人・有名人',
        7 => '同世代',
        22 => 'アニメ・漫画',
        40 => '金融・ビジネス',
        33 => '音楽',
        8 => '地域・暮らし',
        20 => 'ファッション・美容',
        41 => 'イラスト',
        11 => '研究・学習',
        5 => '働き方・仕事',
        2 => '学校・同窓会',
        12 => '料理・グルメ',
        23 => '健康',
        6 => '団体',
        28 => '妊活・子育て',
        19 => '乗り物',
        37 => '写真',
        18 => '旅行',
        27 => '動物・ペット',
        24 => 'TV・VOD',
        29 => '本',
        30 => '映画・舞台',
        0 => 'すべて',
    ];

    const DEFAULT_OPENCHAT_IMG_URL = [
        '1' => '0h6tJf0hQsaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOA',
        '2' => '0h6tJf8QWOaVt3H0eLAsEWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnOQ',
        '3' => '0h6tJfJfGJaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPg',
        '4' => '0h6tJfahRYaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPQ',
        '5' => '0h6tJfzfJQaVt3H0eLAsAWDFheczgHd3wTCTx2eApNKSoefHNVGRdwfgxbdgUMLi8MSngnPFMeNmpbLi8MSngnPFMeNmpbLi8MSngnPw',
    ];

    const OPEN_CHAT_API_DB_MERGER_KILL_FLAG_PATH = __DIR__ . '/../../storage/cron_state/open_chat_api_db_merger_kill_flag.dat';

    const OPEN_CHAT_SUB_CATEGORIES_FILE_PATH = __DIR__ . '/../../storage/open_chat_sub_categories/subcategories.json';

    const OPEN_CHAT_RANKING_POSITION_DIR = __DIR__ . '/../../storage/ranking_position/ranking';

    const OPEN_CHAT_RISING_POSITION_DIR = __DIR__ . '/../../storage/ranking_position/rising';
}
