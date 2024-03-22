<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\Schema\PageBreadcrumbsListSchema;

class PolicyPageController
{
    function index(PageBreadcrumbsListSchema $breadcrumbsShema)
    {
        $_css = ['site_header', 'site_footer', 'room_list', 'terms'];
        $_meta = meta()->setTitle('オプチャグラフについて');
        $_breadcrumbsShema = $breadcrumbsShema->generateSchema('オプチャグラフについて', 'policy');

        return view('policy_content', compact('_meta', '_css', '_breadcrumbsShema'));
    }
}
