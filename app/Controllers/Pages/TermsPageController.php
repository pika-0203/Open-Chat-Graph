<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class TermsPageController
{
    function index()
    {
        $_css = ['site_header_14', 'site_footer_11', 'room_list_17', 'terms'];
        $_meta = meta()->setTitle('利用規約');
        return view('statistics/terms_content', compact('_meta', '_css'));
    }
}
