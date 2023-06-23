<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class PolicyPageController
{
    function index()
    {
        $_css = ['site_header_18', 'site_footer_18', 'room_list_23', 'terms_01'];
        $_meta = meta()->setTitle('ポリシー');
        return view('statistics/policy_content', compact('_meta', '_css'));
    }
}
