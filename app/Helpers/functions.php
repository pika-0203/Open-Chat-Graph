<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Config\ConfigJson;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

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

function convertDatetime(string|int $datetime, bool $time = false): string
{
    $format = 'Y/n/j';

    if (is_int($datetime)) {
        // タイムスタンプが与えられた場合
        if ($time) {
            return date($format . ' H:i', $datetime);
        }
        return date($format, $datetime);
    }

    // 日付文字列をDateTimeImmutableオブジェクトに変換
    $dateTime = new DateTimeImmutable($datetime);

    // 形式を変更して返す
    if ($time) {
        return $dateTime->format($format . ' H:i');
    }
    return $dateTime->format($format);
}

function getCronModifiedDateTime(string $datetime, string $format = 'Y/n/j G:i'): string
{
    $fileTime = OpenChatServicesUtility::getModifiedCronTime($datetime);
    return $fileTime->format($format);
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

function trimOpenChatListDescriptions(array &$openChatList, int $len = 170)
{
    foreach ($openChatList as &$oc) {
        if (isset($oc['description']) && is_string($oc['description'])) {
            $oc['description'] =  mb_strimwidth($oc['description'], 0, $len, '…');
        }
    }
}

function trimOpenChatListDesc(string $desc, int $len = 170)
{
    return mb_strimwidth($desc, 0, $len, '…');
}

function imgUrl(string $img_url): string
{
    return "https://obs.line-scdn.net/{$img_url}";
}

function imgPreviewUrl(string $img_url): string
{
    return "https://obs.line-scdn.net/{$img_url}/preview";
}

function filePathNumById(int $id): string
{
    return (string)floor($id / 1000);
}

function getCategoryName(int $category): string
{
    return AppConfig::OPEN_CHAT_CATEGORY_KEYS[$category] ?? '';
}

function addCronLog(string|array $log)
{
    if (is_string($log)) {
        $log = [$log];
    }

    foreach ($log as $string) {
        error_log(date('Y-m-d H:i:s') . ' ' . $string . "\n", 3, __DIR__ . '/../../logs/cron.log');
    }
}

function isDailyUpdateTime(
    DateTime $currentTime = new DateTime,
    array $start = [23, AppConfig::CRON_START_MINUTE],
    array $end = [0, AppConfig::CRON_START_MINUTE],
    DateTime $nowStart = new DateTime,
    DateTime $nowEnd = new DateTime('+1 day'),
): bool {
    $startTime = $nowStart->setTime(...$start);
    $endTime = $nowEnd->setTime(...$end);

    if ($currentTime > $startTime) return true;
    if ($currentTime < $endTime) return true;
    return false;
}

function checkLineSiteRobots(int $retryLimit = 3, int $retryInterval = 1): string
{
    $retryCount = 0;

    while ($retryCount < $retryLimit) {
        try {
            $robots = file_get_contents('https://openchat.line.me/robots.txt');
            if (!str_contains($robots, 'User-agent: *') || !str_contains($robots, 'Allow: /jp/')) {
                throw new \RuntimeException('Robots.txt: 拒否 ' . $robots);
            }

            return $robots;
        } catch (\Throwable $e) {
            $retryCount++;
            if ($retryCount >= $retryLimit) {
                throw new \RuntimeException(get_class($e) . ': ' . $e->getMessage());
            }

            sleep($retryInterval);
            continue;
        }

        $retryCount++;
        sleep($retryInterval);
    }
}

function getImgSetErrorTag(): string
{
    return <<<HTML
        onerror="this.src='/assets/ogp.png'; this.removeAttribute('onerror'); this.removeAttribute('onload');" onload="this.removeAttribute('onerror'); this.removeAttribute('onload');"
    HTML;
}

function getFilePath($path, $pattern): string
{
    $file = glob(PUBLIC_DIR . "/{$path}/{$pattern}");
    if ($file) {
        $fileName = basename($file[0]);
        return "{$path}/{$fileName}";
    } else {
        return '';
    }
}

function localCORS()
{
    $ip = getIP();
    if ($ip === '::1' || strstr($ip, '192.168') !== false) {
        header('Access-Control-Allow-Origin: *');
    }
}
