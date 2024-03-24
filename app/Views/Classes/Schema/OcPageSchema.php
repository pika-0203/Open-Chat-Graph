<?php

namespace App\Views\Schema;

use Spatie\SchemaOrg\Schema;

class OcPageSchema
{
    function generateSchema(int $open_chat_id, string $name, int $created_at, int $updated_at): string
    {
        $siteUrl = rtrim(url(), '/');
        $sanitizedName = h($name);

        $dataset = Schema::dataset()
            ->name("LINEオープンチャット「{$sanitizedName}」のメンバー数推移")
            ->description(
                "オープンチャット「{$sanitizedName}」のメンバー数の推移を日毎に記録しています。URLにアクセスすると、グラフで表示されます。CSV形式でダウンロードできます。CSVファイルは日付、メンバー数の2カラムの全期間データになっています。"
            )
            ->author(
                Schema::organization()
                    ->name('オプチャグラフ')
                    ->url($siteUrl)
            )
            ->creator(
                Schema::person()
                    ->name('pika-0203')
                    ->url('https://github.com/pika-0203')
            )
            ->keywords([
                "チャットルーム統計",
                "リアルタイムデータ",
                "ユーザー人数"
            ])
            ->provider(
                Schema::organization()
                    ->name('LINE Corporation')
                    ->url('https://line.me/ja/')
            )
            ->license('https://creativecommons.org/licenses/by/4.0/legalcode')
            ->url($siteUrl . '/oc/' . $open_chat_id)
            ->datePublished(new \DateTime('@' . $created_at))
            ->dateModified(new \DateTime('@' . $updated_at))
            ->distribution(
                Schema::dataDownload()
                    ->encodingFormat('CSV')
                    ->contentUrl($siteUrl . '/oc/' . $open_chat_id . '/csv')
            )
            ->measurementTechnique('LINEオープンチャットの公式サイトから人数データを記録');


        // JSON-LDのマークアップを生成
        return $dataset->toScript();
    }
}
