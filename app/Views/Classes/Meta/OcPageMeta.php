<?php

namespace App\Views\Meta;

use App\Views\Meta\Metadata;

class OcPageMeta
{
    function generateMetadata(int $open_chat_id, array $oc): Metadata
    {
        $name = $oc['name'];

        $desc = "オープンチャット「{$name}」の人数・ランキング推移などの統計情報です。コメント機能で意見交換ができます。";

        return meta()
            ->setTitle($name)
            ->setDescription("{$desc}")
            ->setOgpDescription("{$desc}");
    }
}
