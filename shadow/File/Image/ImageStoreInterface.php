<?php

namespace Shadow\File\Image;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
interface ImageStoreInterface
{
    /**
     * Store the given image file to the specified path with the given file name and image type.
     *
     * @param \GdImage    $image      
     * @param string      $destPath  The path to save the image file.
     * @param string|null $fileName  The file name to save as.
     * @param \ImageType  $imageType The image type to save as, defaults to ImageType::WEBP.
     * @param int         $quality   Quality ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file).  
     *                               For PNG, the higher the value, the higher the compression level.
     * 
     * @return string                Returns the file name with extension.
     * 
     * @throws \RuntimeException  Error code: 6000 - If the image function fails.  

     * @throws \InvalidArgumentException
     */
    public function storeImageFromGdImage(
        \GdImage $image,
        string $destPath,
        string $fileName,
        \ImageType|string $imageType = \ImageType::WEBP,
        $quality = 80
    ): string;
}
