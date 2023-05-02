<?php

use Shadow\File\Image\GdImageFactoryInterface;
use Shadow\File\Image\ImageStoreInterface;

class ImageApiController
{
    public function store(
        array $file,
        string $imageType,
        int $imageSize,
        GdImageFactoryInterface $gd,
        ImageStoreInterface $store
    ) {
        try {
            $fileName = $store->storeImageFromGdImage(
                $gd->createGdImage($file, $imageSize, $imageSize),
                publicDir('images'),
                hash('sha224', getIP() . time() . rand()),
                constant("ImageType::{$imageType}")
            );
        } catch (\RuntimeException $e) {
            return redirect('image')
                ->withErrors('image', $e->getCode(), $e->getMessage());
        }

        return redirect('image')
            ->with('image', $fileName);
    }
}
