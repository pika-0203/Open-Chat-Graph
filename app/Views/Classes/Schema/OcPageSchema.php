<?php

namespace App\Views\Schema;

use Spatie\SchemaOrg\Schema;

class OcPageSchema
{
    public string $siteUrl = 'https://openchat-review.me';

    function generateSchema(int $open_chat_id, string $name, int $created_at, int $updated_at): string
    {
        $sanitizedName = h($name);

        $dataset = Schema::dataset()
            ->name("LINEオープンチャット「{$sanitizedName}」のメンバー数推移")
            ->description(
                "オープンチャット「{$sanitizedName}」のメンバー数の推移を日毎に記録しています。URLにアクセスすると、グラフで表示されます。CSV形式でダウンロードできます。CSVファイルは日付、メンバー数の2カラムの全期間データになっています。"
            )
            ->author(
                Schema::organization()
                    ->name('オプチャグラフ')
                    ->url($this->siteUrl)
            )
            ->creator(
                Schema::person()
                    ->name('pika-0203')
                    ->url('https://github.com/pika-0203')
            )
            ->keywords(['LINE', 'オプチャ', 'OpenChat'])
            ->provider(
                Schema::organization()
                    ->name('LINE Corporation')
                    ->url('https://line.me/ja/')
            )
            ->license('https://creativecommons.org/licenses/by/4.0/legalcode')
            ->url($this->siteUrl . '/oc/' . $open_chat_id)
            ->datePublished(dateTimeAttr($created_at))
            ->dateModified(dateTimeAttr($updated_at))
            ->distribution(
                Schema::dataDownload()
                    ->encodingFormat('CSV')
                    ->contentUrl($this->siteUrl . '/oc/' . $open_chat_id)
            );

        // JSON-LDのマークアップを生成
        return $dataset->toScript();
    }
}
