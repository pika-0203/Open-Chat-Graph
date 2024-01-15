<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class PolicyPageController
{
    function index()
    {
        $_css = ['site_header', 'site_footer', 'room_list', 'terms'];
        $_meta = meta()->setTitle('ポリシー');
        return view('policy_content', compact('_meta', '_css'));
    }
}
