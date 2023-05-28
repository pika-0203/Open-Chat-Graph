<?php

namespace App\Views\Schema;

use Spatie\SchemaOrg\Schema;

class OcPageBreadcrumbsListSchema
{
    public string $siteUrl = 'https://openchat-review.me';

    function datasetSchemaMarkup(int $pageNumber): string
    {
        // BreadcrumbListのインスタンスを作成
        $breadcrumbList = Schema::breadcrumbList();

        $itemListElement = [
            Schema::listItem()
                ->position(1)
                ->name('トップ')
                ->item($this->siteUrl),
            Schema::listItem()
                ->position(2)
                ->name('急上昇ランキング')
                ->item($this->siteUrl . '/ranking'),
        ];

        if ($pageNumber > 1) {
            $itemListElement[] = Schema::listItem()
                ->position(3)
                ->name('ページ' . $pageNumber)
                ->item($this->siteUrl . '/ranking/' . $pageNumber);
        }

        // リストの要素を追加
        $breadcrumbList->itemListElement($itemListElement);

        // JSON-LDのマークアップを生成
        return $breadcrumbList->toScript();
    }
}
