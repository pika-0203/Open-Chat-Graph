<?php

namespace App\Views\Meta;

use App\Views\Metadata;

class OcPageMeta
{
    function generateMetadata(array $oc): Metadata
    {
        $diffMember = '';
        if ($oc['diff_member'] ?? 0 !== 0) {
            $diffNum = signedNum($oc['diff_member']);
            $diffPer = signedNum(singnedCeil($oc['percent_increase'] * 10) / 10);
            $diffMember = ", 前日比:{$diffNum}({$diffPer}%)";
        } elseif ($oc['diff_member'] === 0) {
            $diffMember = ', 前日比:±0';
        }

        $name = $oc['name'];
        $date = date('Y/m/d', $oc['updated_at']);
        $memberNum = $oc['member'];

        $desc = "オープンチャット「{$name}」の人数推移をグラフで表示します。オプチャの人気度や活性度がチェック出来ます！【{$date}】メンバー数:{$memberNum}{$diffMember}";

        $ogpDate = date('m/d', $oc['updated_at']);
        $ogpDesc = "オプチャの人数推移を分析。[{$ogpDate}]メンバー数:{$memberNum}{$diffMember}";

        return meta()->setTitle($name)->setDescription($desc)->setOgpDescription($ogpDesc);
    }
}
