<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class TermsPageController
{
    function index()
    {
        $_css = ['site_header_13', 'site_footer_7', 'room_list_14', 'terms'];
        $_meta = meta()->setTitle('利用規約');
        return view('statistics/terms_content', compact('_meta', '_css'));
    }
}
