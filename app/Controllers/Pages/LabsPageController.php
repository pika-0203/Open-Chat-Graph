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

        $_css = ['room_page_28', 'site_header_21', 'site_footer_18', 'labs_03'];

        return view('statistics/labs_content', compact('_meta', '_css'));
    }

    function live()
    {
        $name = 'ライブトーク利用時間分析ツール';
        $desc = "トーク履歴を読み込ませることで、ライブトークの利用時間をグラフと共に確認することができます。";
        $_meta = meta()->setTitle($name)->setDescription($desc)->setOgpDescription($desc);

        $_css = ['room_page_28', 'site_header_21', 'site_footer_18', 'live_ana_03'];

        return view('statistics/live_content', compact('_meta', '_css'));
    }
}
