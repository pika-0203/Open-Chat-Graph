<?php

namespace App\Views\Meta;

use App\Views\Meta\Metadata;

class OcPageMeta
{
    function generateMetadata(int $open_chat_id, array $oc): Metadata
    {
        $name = $oc['name'];

        $desc = "オープンチャット「{$name}」の人数・ランキング推移などの分析情報が見れます。コメント機能で意見交換ができます。";

        return meta()
            ->setTitle($name)
            ->setDescription("{$desc}")
            ->setOgpDescription("{$desc}");
    }
}
