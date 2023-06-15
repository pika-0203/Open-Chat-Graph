<?php

namespace App\Config;

class OpenChatCrawlerConfig
{
    const LINE_URL_MATCH_PATTERN = '{(?<=https:\/\/line\.me\/ti\/g2\/).+?(?=\?|$)}';

    const DOM_CLASS_NAME = '.MdMN04Txt';
    const DOM_CLASS_MEMBER = '.MdMN05Txt';
    const DOM_CLASS_DESCRIPTION = '.MdMN06Desc';
    const DOM_CLASS_IMG = '.mdMN01Img';

    const LINE_IMG_URL = 'https://obs.line-scdn.net/';
    const LINE_IMG_PREVIEW_PATH = '/preview';
    const IMG_MIME_TYPE = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    
    const STORE_IMG_QUALITY = 50;
    const SOTRE_IMG_DEST_PATH = __DIR__ . '/../../public/oc-img';
    const SOTRE_IMG_PREVIEW_DEST_PATH = __DIR__ . '/../../public/oc-img/preview';
}
