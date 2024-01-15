<?php

declare(strict_types=1);

namespace Shadow\File\Image;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ImageStore implements ImageStoreInterface
{
    public function storeImageFromGdImage(
        \GdImage $image,
        string $destPath,
        string $fileName,
        \ImageType|string $imageType = \ImageType::WEBP,
        $quality = 80
    ): string {
        $destPath = "/" . ltrim(rtrim($destPath, "/"), "/") . "/";

        if (!is_writable($destPath)) {
            throw new \InvalidArgumentException('Destination path is not writable.');
        }

        if ($quality < 0 || $quality > 100) {
            throw new \InvalidArgumentException('Invalid image quality. Quality must be between 0 and 100.');
        }

        if (is_string($imageType)) {
            $type = $imageType;
            if (!function_exists("image{$type}")) {
                throw new \InvalidArgumentException('"image{$type}" is not a function.');
            }
        } else {
            $type = $imageType->value;
        }

        if ($type === 'png') {
            $quality = $this->convertToSingleDigitWithBias($quality);
        }

        $imageFunction = "image{$type}";

        $fileName = $this->escapeInvalidCharacters($fileName);

        if ($fileName === '') {
            throw new \InvalidArgumentException('File name is empty.');
        }

        if (strpos($fileName, '.') === false) {
            $fileName .= ".{$type}";
        }

        $filePath = $destPath . $fileName;

        if (!$imageFunction($image,  $filePath, $quality) || !file_exists($filePath)) {
            throw new \RuntimeException("{$imageFunction} fails", 6000);
        }

        return $fileName;
    }

    protected function escapeInvalidCharacters(string $filename): string
    {
        $invalidCharacters = '/ \ : * ? " < > | % ( ) ! @ # $ & + , ; =';
        return preg_replace('/[' . preg_quote($invalidCharacters, '/') . ']/', '_', $filename);
    }

    /**
     * Convert image quality value to a single digit integer between 0 and 9 with a bias.
     *
     * @param int $num The image quality value to convert. Must be between 0 and 100.
     * 
     * @return int A single digit integer between 0 and 9.
     * 
     */
    protected function convertToSingleDigitWithBias(int $num): int
    {
        if ($num === 80) {
            return 6;
        }

        if ($num < 80) {
            // Convert the range 0-80 to 0-6 with a linear interpolation
            $result = $num / 80 * 6;
        } else {
            // Convert the range 80-100 to 6-9 with a linear interpolation
            $result = 6 + ($num - 80) / 20 * 3;
        }

        return round($result);
    }
}
