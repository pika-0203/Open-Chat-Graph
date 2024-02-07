<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class RegisterPageController
{
    function index()
    {
        $_css = ['room_list', 'site_header', 'site_footer'];
        $_meta = meta()->setTitle('オープンチャットを登録');
        return view('register_form_content', compact('_css', '_meta'));
    }
}
