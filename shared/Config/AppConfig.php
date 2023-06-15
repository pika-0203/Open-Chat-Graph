<?php

namespace App\Config;

class AppConfig
{
    const DEVICE_COOKIE_EXPIRES = 60 * 60 * 24 * 30;

    const LINE_URL = 'https://line.me/ti/g2/';
    const LINE_IMG_PREVIEW_SUFFIX = '_p';
    const OPENCHAT_IMG_PATH = '/oc-img/';
    const OPENCHAT_IMG_PREVIEW_PATH = '/oc-img/preview/';

    const OPEN_CHAT_LIST_LIMIT = 50;

    const CRON_EXECUTE_COUNT = 500;
    const CRON_API_KEY = '/^1234567890$/';

    const LINE_OPEN_URL = 'https://line.me/ti/g2/';

    /**
     * @var string `['openChatList' => array, 'updatedAt' => int]`
     */
    const FILEPATH_TOP_RANKINGLIST = 'top_content/rankingList.dat';

    const SITEMAP_DIR = __DIR__ . '/../../public/sitemap.xml';
}
