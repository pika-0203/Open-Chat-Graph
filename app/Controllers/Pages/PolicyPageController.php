<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Views\Content\TopPageNews;
use App\Views\Schema\PageBreadcrumbsListSchema;

class PolicyPageController
{
    function index(PageBreadcrumbsListSchema $breadcrumbsShema)
    {
        $_css = ['site_header', 'site_footer', 'room_list', 'terms'];
        $_meta = meta()->setTitle('オプチャグラフについて');
        $_meta->image_url = '';
        $desc = 'オプチャグラフはユーザーがオプチャの成長傾向を比較できます。オープンソースのLINE非公式サイトです。';
        $_meta->setDescription($desc)->setOgpDescription($desc);
        $_breadcrumbsShema = $breadcrumbsShema->generateSchema('オプチャグラフについて', 'policy');

        $_news = array_reverse(TopPageNews::getTopPageNews());

        cacheControl(3600);

        return view('policy_content', compact('_meta', '_css', '_breadcrumbsShema','_news'));
    }

    function privacy(PageBreadcrumbsListSchema $breadcrumbsShema)
    {
        $_css = ['site_header', 'site_footer', 'room_list', 'terms'];
        $_meta = meta()->setTitle('プライバシーポリシー');
        $_meta->image_url = '';
        $desc = 'オプチャグラフはユーザーがオプチャの成長傾向を比較できます。オープンソースのLINE非公式サイトです。';
        $_meta->setDescription($desc)->setOgpDescription($desc);
        $_breadcrumbsShema = $breadcrumbsShema->generateSchema('オプチャグラフについて', 'policy', 'プライバシーポリシー', 'privacy');

        return view('privacy_content', compact('_meta', '_css', '_breadcrumbsShema'));
    }
}
