<?php

declare(strict_types=1);

namespace App\Services\Recommend;

class RecommendUtility
{
    const OmitPettern = [
        'ディズニー ツムツム' => 'ツムツム',
        '歌リレー' => 'ボイメ 歌',
        '歌い手のトークルーム' => '歌い手',
        'ゼベワン' => 'ZB1',
        '全也' => 'なりきり',
        'スプラトゥーン' => 'スプラ',
        '妖怪ウォッチ ぷにぷに' => 'ぷにぷに',
        'ONE PIECE バウンティラッシュ' => 'バウンティ',
        '中学生・高校生限定' => '中高生限定',
        '小学生・中学生限定' => '小中学生限定',
        '葬送のフリーレン' => 'フリーレン',
        'クーポン・お得情報' => 'クーポン',
        '東方Project' => '東方',
        'ツイステッドワンダーランド' => 'ツイステ',
    ];

    static function extractTag(string $str): string
    {
        // 文末の括弧内のテキストを抽出する正規表現
        if (preg_match('/（(.*)）$/', $str, $matches)) {
            // 括弧内のテキストが見つかった場合
            $textInsideParentheses = $matches[1];

            // 「／」で分割し、最後の要素を取得
            $parts = explode('／', $textInsideParentheses);
            $str = array_pop($parts);
        }

        return self::OmitPettern[$str] ?? $str;
    }
}
