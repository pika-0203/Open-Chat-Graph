<?php

declare(strict_types=1);

namespace Shadow\File\Image;

class GdImageFactory implements GdImageFactoryInterface
{
    public function createGdImage(string|array $imageData, ?int $maxWidth = null, ?int $maxHeight = null): \GdImage
    {
        [$imageData, $srcImage] = $this->imageCreateFromString($imageData);
        if (!$srcImage) {
            throw new \RuntimeException('Unable to create image from the given data.', 5001);
        }

        [$srcWidth, $srcHeight] = getimagesizefromstring($imageData);

        if (!$srcWidth || !$srcHeight) {
            throw new \RuntimeException('Unable to get size of the given image data.', 5002);
        }

        [$dstWidth, $dstHeight] = $this->getNewSize($srcWidth, $srcHeight, $maxWidth, $maxHeight);

        $dstImage = $this->imageCreate($dstWidth, $dstHeight);

        $this->processAlphaChannel($srcImage, $dstImage);

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        imagedestroy($srcImage);

        return $dstImage;
    }

    private function imageCreateFromString(string|array $imageData): array|false
    {
        if (is_array($imageData) && isset($imageData['tmp_name']) && file_exists($imageData['tmp_name'])) {
            $imageData = file_get_contents($imageData['tmp_name']);
        }

        if (is_string($imageData)) {
            return [$imageData, imagecreatefromstring($imageData)];
        } else {
            throw new \RuntimeException('Invalid file data in the array.', 5001);
        }
    }

    private function getNewSize(int $srcWidth, int $srcHeight, ?int $maxWidth, ?int $maxHeight): array
    {
        $dstWidth = $srcWidth;
        $dstHeight = $srcHeight;

        if ($maxWidth && $srcWidth > $maxWidth) {
            $dstWidth = $maxWidth;
            $dstHeight = intval(($dstWidth / $srcWidth) * $srcHeight);
        }

        if ($maxHeight && $dstHeight > $maxHeight) {
            $dstHeight = $maxHeight;
            $dstWidth = intval(($dstHeight / $srcHeight) * $srcWidth);
        }

        return [$dstWidth, $dstHeight];
    }

    private function imageCreate(int $width, int $height): \GdImage
    {
        $image = imagecreatetruecolor($width, $height);

        if (!$image) {
            throw new \RuntimeException('Failed to create a new image.', 5003);
        }

        return $image;
    }

    private function processAlphaChannel(\GdImage $srcImage, \GdImage $dstImage)
    {
        $isAlpha = false;
        if (function_exists('imageistruecolor') && !imageistruecolor($srcImage)) {
            $isAlpha = imagecolortransparent($srcImage) != -1 || imagecolorstotal($srcImage) > 255;
        } else {
            $isAlpha = imagealphablending($srcImage, false);
            $color = imagecolorallocatealpha($srcImage, 0, 0, 0, 127);
            $isAlpha = $isAlpha || (imagecolorat($srcImage, 0, 0) & 0x7F000000) != 0x7F000000 || imagecolorclosestalpha($srcImage, 0, 0, 0, 127) != $color;
        }
        if ($isAlpha) {
            imagepalettetotruecolor($srcImage);
            imagealphablending($srcImage, true);
            imagesavealpha($srcImage, true);
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }
    }
}
