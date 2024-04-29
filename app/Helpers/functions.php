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
            return date($format . ' G:i', $datetime);
        }
        return date($format, $datetime);
    }

    // 日付文字列をDateTimeImmutableオブジェクトに変換
    $dateTime = new DateTimeImmutable($datetime);

    // 形式を変更して返す
    if ($time) {
        return $dateTime->format($format . ' G:i');
    }
    return $dateTime->format($format);
}

function timeElapsedString(string $datetime, int $thresholdMinutes = 15): string
{
    $now = new DateTimeImmutable();
    $interval = $now->diff(new DateTimeImmutable($datetime));

    $totalMinutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

    if ($totalMinutes <= $thresholdMinutes) {
        return 'たった今';
    } elseif ($interval->y > 0) {
        return $interval->y . '年前';
    } elseif ($interval->m > 0) {
        return $interval->m . 'ヶ月前';
    } elseif ($interval->d > 0) {
        return $interval->d . '日前';
    } elseif ($interval->h > 0) {
        return $interval->h . '時間前';
    } elseif ($interval->i > 0) {
        return $interval->i . '分前';
    } else {
        return $interval->s . '秒前';
    }
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

/* function imgUrl(int $id, string $img_url): string
{
    return "https://obs.line-scdn.net/{$img_url}";
}

function imgPreviewUrl(int $id, string $img_url): string
{
    return "https://obs.line-scdn.net/{$img_url}/preview";
} */

function imgUrl(int $id, string $local_img_url): string
{
    return url((in_array(
        $local_img_url,
        AppConfig::DEFAULT_OPENCHAT_IMG_URL_HASH
    ) ? AppConfig::OPENCHAT_IMG_PATH . "/default/{$local_img_url}.webp?id={$id}" : getImgPath($id, $local_img_url)));
}

function imgPreviewUrl(int $id, string $local_img_url): string
{
    return url((
        in_array($local_img_url, AppConfig::DEFAULT_OPENCHAT_IMG_URL_HASH)
        ? AppConfig::OPENCHAT_IMG_PATH . '/' . AppConfig::OPENCHAT_IMG_PREVIEW_PATH . "/default/{$local_img_url}" . AppConfig::OPENCHAT_IMG_PREVIEW_SUFFIX . ".webp?id={$id}"
        : getImgPreviewPath($id, $local_img_url)
    ));
}

function apiImgUrl(int $id, string $local_img_url): string
{
    return in_array($local_img_url, AppConfig::DEFAULT_OPENCHAT_IMG_URL_HASH)
        ? ("default/{$local_img_url}" . AppConfig::OPENCHAT_IMG_PREVIEW_SUFFIX . ".webp?id={$id}")
        : (filePathNumById($id) . "/{$local_img_url}" . AppConfig::OPENCHAT_IMG_PREVIEW_SUFFIX . ".webp");
}

/**
 * @return string oc-img/{$idPath}/{$imgUrl}.webp
 */
function getImgPath(int $open_chat_id, string $imgUrl): string
{
    $subDir = filePathNumById($open_chat_id);
    return AppConfig::OPENCHAT_IMG_PATH . "/{$subDir}/{$imgUrl}.webp";
}

/**
 * @return string oc-img/preview/{$idPath}/{$imgUrl}_p.webp
 */
function getImgPreviewPath(int $open_chat_id, string $imgUrl): string
{
    $subDir = filePathNumById($open_chat_id);
    return AppConfig::OPENCHAT_IMG_PATH . '/' . AppConfig::OPENCHAT_IMG_PREVIEW_PATH . "/{$subDir}/{$imgUrl}" . AppConfig::OPENCHAT_IMG_PREVIEW_SUFFIX . ".webp";
}

function filePathNumById(int $id): string
{
    return (string)floor($id / 1000);
}

function getCategoryName(int $category): string
{
    return array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] ?? '';
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
    DateTime $nowEnd = new DateTime,
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
    if (strstr($ip, '::1') !== false || strstr($ip, '192.168') !== false) {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
            exit;
        }
    }
}

function formatMember(int $n)
{
    return $n < 1000 ? $n : ($n >= 10000 ? (floor($n / 1000) / 10 . '万') : number_format($n));
}

function sortAndUniqueArray(array $array, int $min = 2)
{
    // 各要素の出現回数をカウント
    $counts = array_count_values(array_filter($array, fn ($el) => is_string($el) || is_int($el) || $el));

    // 出現回数が2以上の要素のみを保持
    $filteredCounts = array_filter($counts, fn ($count) => $count >= $min);

    // 出現回数の多い順にソート（同じ出現回数の場合は元の順序を保持）
    uksort($filteredCounts, function ($a, $b) use ($filteredCounts) {
        if ($filteredCounts[$a] === $filteredCounts[$b]) {
            return 0;
        }
        return $filteredCounts[$a] < $filteredCounts[$b] ? 1 : -1;
    });

    // キーのみを抽出（重複排除）
    return array_keys($filteredCounts);
}