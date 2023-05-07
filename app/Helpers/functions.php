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
