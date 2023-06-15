<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class PolicyPageController
{
    function index()
    {
        $_css = ['site_header_17', 'site_footer_15', 'room_list_21', 'terms_01'];
        $_meta = meta()->setTitle('ポリシー');
        return view('statistics/policy_content', compact('_meta', '_css'));
    }
}
