<?php

declare(strict_types=1);

function deleteFile(string $file): bool
{
    if (file_exists($file)) {
        return unlink($file);
    }
    return false;
}

function meta(): App\Views\Metadata
{
    return new App\Views\Metadata;
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

function singnedCeil(int|float $num): float
{
    if ($num < 0) {
        return floor($num);
    } else {
        return ceil($num);
    }
}

function rankingPagerUrl(int $pageNumber): string
{
    $path = ($pageNumber > 1) ? "/ranking/" . (string) $pageNumber : '';
    return \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost() . $path;
}

function statisticsComponent(string $string, ?array $var = null): void
{
    if ($var) {
        extract($var);
    }
    include __DIR__ . '/../Views/statistics/components/' . $string . '.php';
}

function searchPager(string $keyword, int $pageNumber, string $nameQ = 'q', string $nameP = 'p', string $path = 'search'): string
{
    $query = http_build_query([$nameQ => $keyword]);
    $page = ($pageNumber > 1) ? '&' . http_build_query([$nameP => $pageNumber]) : '';
    return \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost() . "/{$path}?{$query}{$page}";
}

function path(): string
{
    return $_SERVER['REQUEST_URI'] ?? '';
}

function dateTimeAttr(int $timestamp): string
{
    return date('Y-m-d\TH:i:sO', $timestamp);
}

function getDailyRankingDateTime(int $timestamp): string
{
    return date('Y.m.d H:i', $timestamp);
}

function saveArrayToFile(string $filename, array $array)
{
    $json = json_encode($array);
    file_put_contents(__DIR__ . '/../../strage/' . $filename, $json);
}

function getArrayFromFile(string $filename)
{
    $json = file_get_contents(__DIR__ . '/../../strage/' . $filename);
    return json_decode($json, true);
}
