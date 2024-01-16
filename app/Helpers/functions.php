<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Config\ConfigJson;

/**
 * Inserts HTML line breaks before all newlines in a string.
 *
 * @param string $string The input string to be processed.
 * @return string The string with HTML line breaks.
 */
function nl2brReplace(string $string): string
{
    $lines = preg_split('/\r\n|\r|\n/', $string);
    $result = implode("<br>", $lines);
    return $result;
}

function gTag(string $id): string
{
    return
        <<<HTML
        <script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
        
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', '{$id}');
        </script>
        HTML;
}

function meta(): App\Views\Meta\Metadata
{
    return new App\Views\Meta\Metadata;
}

function signedNum(int|float $num): string
{
    if ($num < 0) {
        return (string)$num;
    } elseif ($num > 0) {
        return '+' . $num;
    } else {
        return '0';
    }
}

function signedNumF(int|float $num): string
{
    if ($num < 0) {
        return number_format($num);
    } elseif ($num > 0) {
        return '+' . number_format($num);
    } else {
        return '0';
    }
}

function signedCeil(int|float $num): float
{
    if ($num < 0) {
        return floor($num);
    } else {
        return ceil($num);
    }
}

function viewComponent(string $string, ?array $var = null): void
{
    if ($var) {
        extract($var);
    }
    include __DIR__ . '/../Views/components/' . $string . '.php';
}

function dateTimeAttr(int $timestamp): string
{
    return date('Y-m-d\TH:i:sO', $timestamp);
}

function convertDatetimeAndOneDayBefore(string|int $datetime, bool $time = false)
{
    if (is_int($datetime)) {
        // タイムスタンプが与えられた場合
        $previousDayTimestamp = $datetime - 86400; // 一日は 86400 秒
        if ($time) {
            return gmdate('Y.m.d H:i', $previousDayTimestamp);
        }
        return gmdate('Y.m.d', $previousDayTimestamp);
    }

    // 日付文字列を処理する場合、DateTimeImmutableを使用
    $dateTime = new DateTimeImmutable($datetime);

    // 一日前の日付を計算
    $previousDay = $dateTime->sub(new DateInterval('P1D'));

    // 形式を変更して返す
    if ($time) {
        return $previousDay->format('Y.m.d H:i');
    }
    return $previousDay->format('Y.m.d');
}

function convertDatetime(string|int $datetime, bool $time = false)
{
    if (is_int($datetime)) {
        // タイムスタンプが与えられた場合
        if ($time) {
            return date('Y.m.d H:i', $datetime);
        }
        return date('Y.m.d', $datetime);
    }

    // 日付文字列をDateTimeImmutableオブジェクトに変換
    $dateTime = new DateTimeImmutable($datetime);

    // 形式を変更して返す
    if ($time) {
        return $dateTime->format('Y.m.d H:i');
    }
    return $dateTime->format('Y.m.d');
}

function getHostAndUri(): string
{
    return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function getQueryString(string $separater = '?'): string
{
    return $_SERVER['QUERY_STRING'] ? $separater . $_SERVER['QUERY_STRING'] : '';
}

function cache()
{
    header('Cache-Control: private');
}

function aliveStyleColor(array $oc)
{
    if (($oc['is_alive'] ?? null) === 0) echo 'style="color:#cf1c1c"';
}

function trimOpenChatListDescriptions(array &$openChatList, int $len = 170)
{
    foreach ($openChatList as &$oc) {
        if (isset($oc['description']) && is_string($oc['description'])) {
            $oc['description'] =  mb_strimwidth($oc['description'], 0, $len, '…');
        }
    }
}

function imgUrl(int $open_chat_id, string $img_url): string
{
    if ($img_url === 'noimage') {
        $img_url = AppConfig::DEFAULT_OPENCHAT_IMG_URL['1'];
    }

    return "https://obs.line-scdn.net/{$img_url}.jpg";
}

function imgUrlLocal(int $open_chat_id, string $img_url): string
{
    if ($img_url === 'noimage') {
        $img_url = AppConfig::DEFAULT_OPENCHAT_IMG_URL['1'];
        $open_chat_id = 1;
    }

    return url(AppConfig::OPENCHAT_IMG_PATH . (string)floor($open_chat_id / 1000) . "/{$img_url}.webp");
}

function imgPreviewUrl(int $open_chat_id, string $img_url): string
{
    if ($img_url === 'noimage') {
        $img_url = AppConfig::DEFAULT_OPENCHAT_IMG_URL['1'];
        $open_chat_id = 1;
    }

    return url(AppConfig::OPENCHAT_IMG_PATH . (string)floor($open_chat_id / 1000) . AppConfig::OPENCHAT_IMG_PREVIEW_PATH . "{$img_url}" . AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp');
}

function imgPreviewUrlLocal(int $open_chat_id, string $img_url): string
{
    if ($img_url === 'noimage') {
        $img_url = AppConfig::DEFAULT_OPENCHAT_IMG_URL['1'];
        $open_chat_id = 1;
    }

    return url(AppConfig::OPENCHAT_IMG_PATH . (string)floor($open_chat_id / 1000) . AppConfig::OPENCHAT_IMG_PREVIEW_PATH . "{$img_url}" . AppConfig::LINE_IMG_PREVIEW_SUFFIX . '.webp');
}

function filePathNumById(int $id): string
{
    return (string)floor($id / 1000);
}

function getCategoryName(int $category): string
{
    return AppConfig::OPEN_CHAT_CATEGORY_KEYS[$category] ?? '';
}

function getSeachBannedIdQuery(string $column): string
{
    /** @var ConfigJson $json */
    $json = app(ConfigJson::class);
    if (!$json->searchBan) {
        return '0';
    }

    $where = [];
    foreach ($json->searchBan as $id) {
        $where[] = "{$column} = {$id}";
    }

    return '(' . implode(' OR ', $where) . ')';
}

function addCronLog(string $string)
{
    error_log(date('Y-m-d H:i:s') . ' ' . $string . "\n", 3, __DIR__ . '/../../logs/cron.log');
}

function excludeTime(array $start = [11, 30, 0], array $end = [12, 30, 0]): bool
{
    $currentTime = new DateTime;
    $updateTime = (new DateTime)->setTime(...$start);
    $updateTimeRange = (new DateTime)->setTime(...$end);
    return ($currentTime > $updateTime) && ($currentTime < $updateTimeRange);
}
