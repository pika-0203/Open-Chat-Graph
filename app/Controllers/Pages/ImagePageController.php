<?php

class ImagePageController
{
    function index()
    {
        $title = 'Image Uploader';
        $link = 'https://github.com/mimimiku778/MimimalCMS-v0.1';

        return view('test_header', compact('title'))
            ->make('test_image_uploader')
            ->make('test_footer',  compact('link'));
    }
}
