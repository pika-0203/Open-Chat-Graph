<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Config\AppConfig;
use Shadow\DB;

class RecommendUpdater
{
    const AGS = [
        "ボイメ 歌",
        "ライブトーク",
    ];

    /** @var string[] $tags */
    public array $tags;

    private function replace(string $str, string $column): string
    {
        $count = 0;
        $str3 = "{$column} LIKE '%" . str_replace(' ', "%' AND {$column} LIKE '%", $str, $count) . "%'";
        if ($count) return $str3;

        $str2 = "{$column} LIKE '%" . str_replace('_OR_', "%' OR {$column} LIKE '%", $str, $count) . "%'";
        return $count ? $str2 : $str3;
    }

    /** @return string[] */
    private function getReplacedTags(string $column): array
    {
        $this->tags = array_merge(
            self::AGS,
            array_merge(...json_decode(
                file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_TAG_FILE_PATH),
                true
            ))
        );

        return array_map(fn ($str) => $this->replace($str, $column), $this->tags);
    }

    function updateName(string $column = 'oc.name', string $table = 'recommend')
    {
        $tags = $this->getReplacedTags($column);

        foreach ($tags as $key => $search) {
            $tag = $this->tags[$key];
            DB::fetch(
                "INSERT IGNORE INTO
                    {$table}
                SELECT
                    oc.id,
                    '{$tag}',
                    1
                FROM
                    open_chat AS oc
                WHERE
                    {$search}"
            );
        }
    }

    /** @return array{ string:string[] }  */
    private function getReplacedTagsDesc(string $column): array
    {
        $this->tags = json_decode((file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_FILE_PATH)), true);

        return array_map(fn ($a) => array_map(fn ($str) => $this->replace($str, $column), $a), $this->tags);
    }

    function updateDescription(string $column = 'oc.description', string $table = 'recommend')
    {
        $tags = $this->getReplacedTagsDesc($column);

        foreach ($tags as $category => $array) {
            foreach ($array as $key => $search) {
                $tag = $this->tags[$category][$key];

                DB::fetch(
                    "INSERT IGNORE INTO
                        {$table}
                    SELECT
                        oc.id,
                        '{$tag}',
                        2
                    FROM
                        open_chat AS oc
                    WHERE
                        {$search}
                        AND category = {$category}"
                );
            }
        }
    }

    function updateRecommendTables()
    {
        DB::transaction(function () {
            DB::execute("DELETE FROM oc_tag");
            DB::execute("DELETE FROM recommend");
            $this->updateName();
            $this->updateDescription();
            $this->updateDescription('oc.name', 'oc_tag');
            $this->updateDescription(table: 'oc_tag');
            $this->updateName(table: 'oc_tag');
            $this->updateName('oc.name', 'oc_tag');
        });
    }
}
