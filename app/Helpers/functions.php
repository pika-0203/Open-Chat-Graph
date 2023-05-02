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