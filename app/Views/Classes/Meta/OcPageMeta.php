<?php

namespace App\Views\Meta;

use App\Views\Meta\Metadata;

class OcPageMeta
{
    function generateMetadata(int $open_chat_id, array $oc): Metadata
    {
        $name = $oc['name'];

        $desc = "オープンチャット「{$name}」の人数・ランキング推移などの統計情報とコメント機能";

        return meta()
            ->setTitle($name)
            ->setDescription("{$desc}")
            ->setOgpDescription("{$desc}");
    }
}
