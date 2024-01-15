<?php

namespace App\Views\Meta;

use App\Views\Meta\Metadata;

class OcPageMeta
{
    function generateMetadata(int $open_chat_id, array $oc): Metadata
    {
        $name = $oc['name'];
        if (isset($oc['is_alive']) && $oc['is_alive'] === 0) {
            $name = '【削除済み】' . $name;
        }

        $ocDesc = mb_strimwidth($oc['description'], 0, 100, '…');

        $memberNum = number_format($oc['member']);
        $statsDesc = "メンバー数 {$memberNum}人";
        $ogpDesc = "メンバー {$memberNum}";

        if ($oc['diff_member'] === null && $oc['diff_member2'] === null) {
            $desc = "オープンチャットの推移をグラフ表示";
            return meta()
                ->setTitle($name)
                ->setDescription("{$desc}｜{$statsDesc}｜{$ocDesc}")
                ->setOgpDescription("{$desc}｜{$ogpDesc}｜{$ocDesc}");
        }

        $lastUpdate = date('m/d', $oc['updated_at']);

        if ($oc['diff_member2'] !== null) {
            $previousWeek = date('m/d', strtotime('-1 week', $oc['updated_at']));
            [$diffMember2, $diffMemberOgp2] = $this->getDiffMember($oc['diff_member2'], $oc['percent_increase2']);

            $statsDesc .= " ({$previousWeek}~{$lastUpdate} {$diffMember2})";
            //$ogpDesc .= " ({$previousWeek}~{$lastUpdate} {$diffMemberOgp2})";
        } else if ($oc['diff_member'] !== null) {
            $previous = date('Y/m/d', strtotime('-1 day', $oc['updated_at']));
            $previousOgp = date('m/d', strtotime('-1 day', $oc['updated_at']));
            [$diffMember, $diffMemberOgp] = $this->getDiffMember($oc['diff_member'], $oc['percent_increase']);

            $statsDesc .= " ({$previous}~{$lastUpdate} {$diffMember})";
            //$ogpDesc .= " ({$previousOgp}~{$lastUpdate} {$diffMemberOgp})";
        }

        if ($oc['api_created_at'] !== null) {
            $createdAt = convertDatetime($oc['api_created_at']);

            $statsDesc .= "・作成日 {$createdAt}";
            $ogpDesc .= "・作成 {$createdAt}";
        }

        return meta()
            ->setTitle($name)
            ->setDescription("{$statsDesc}｜{$ocDesc}")
            ->setOgpDescription("{$ogpDesc}｜{$ocDesc}");
    }

    private function getDiffMember(int $diff_member, float $percent_increase): array
    {
        if ($diff_member === 0) {
            return ['±0人', '±0'];
        } else {
            $diffNum = signedNumF($diff_member);
            //$diffPer = signedNum(signedCeil($percent_increase * 10) / 10);
            //return ["{$diffNum}人({$diffPer}%)", "{$diffNum}({$diffPer}%)"];
            return ["{$diffNum}人", "{$diffNum}"];
        }
    }
}
