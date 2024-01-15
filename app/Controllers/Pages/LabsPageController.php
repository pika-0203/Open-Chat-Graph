<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

class LabsPageController
{
    function index()
    {
        $name = 'Labs（ラボ）';
        $desc = "試作中の機能をLabs（ラボ）として提供しています。";
        $_meta = meta()->setTitle($name)->setDescription($desc)->setOgpDescription($desc);

        $_css = ['room_page', 'site_header', 'site_footer', 'labs'];

        return view('labs_content', compact('_meta', '_css'));
    }

    function live()
    {
        $name = 'ライブトーク利用時間分析ツール';
        $desc = "オープンチャットのトーク履歴から、ライブトークの通話時間・開催メンバーをグラフで表示することができます。";
        $ogpDesc = "トーク履歴から、ライブトークの利用時間をグラフで表示することができます。";
        $_meta = meta()->setTitle($name)->setDescription($desc)->setOgpDescription($ogpDesc);

        $_css = ['room_page', 'site_header', 'site_footer', 'live_ana'];

        return view('live_content', compact('_meta', '_css'));
    }
}
