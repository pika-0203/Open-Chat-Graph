<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class TermsPageController
{
    function index()
    {
        $_css = ['site_header_10', 'site_footer_6', 'room_list_12', 'terms'];
        $_meta = meta()->setTitle('利用規約');
        return view('statistics/terms_content', compact('_meta', '_css'));
    }
}
