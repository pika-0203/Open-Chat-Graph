<?php

namespace App\Views\Schema;

use Spatie\SchemaOrg\Schema;

class PageBreadcrumbsListSchema
{
    function generateSchema(string $listItemName, string $path): string
    {
        // BreadcrumbListのインスタンスを作成
        $breadcrumbList = Schema::breadcrumbList();

        $itemListElement = [
            Schema::listItem()
                ->position(1)
                ->name('トップ')
                ->item(url()),
            Schema::listItem()
                ->position(2)
                ->name($listItemName)
                ->item(url($path)),
        ];

        // リストの要素を追加
        $breadcrumbList->itemListElement($itemListElement);

        // JSON-LDのマークアップを生成
        return $breadcrumbList->toScript();
    }
}
