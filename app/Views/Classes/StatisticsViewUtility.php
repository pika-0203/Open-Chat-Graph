<?php

declare(strict_types=1);

namespace App\Views;

class StatisticsViewUtility
{
    function getOcPageArrayElementMemberDiff(array $statisticsData): array
    {
        $result = [
            'diff_member' => null,
            'percent_increase' => null,
            'diff_member2' => null,
            'percent_increase2' => null,
        ];

        if (count($statisticsData['date']) < 2) {
            return $result;
        }

        $values = $this->areDatesConsecutive($statisticsData['date'], $statisticsData['member']);
        if (!$values) {
            return $result;
        }

        [
            'diffNum' => $result['diff_member'],
            'percentIncrease' => $result['percent_increase']
        ] = $this->calculateDiff(...$values);

        $oneWeekBeforeValue = $this->findOneWeekBefore($statisticsData['date'], $statisticsData['member']);
        if ($oneWeekBeforeValue !== false) {
            [
                'diffNum' => $result['diff_member2'],
                'percentIncrease' => $result['percent_increase2']
            ] = $this->calculateDiff($values[0], $oneWeekBeforeValue);
        }

        return $result;
    }

    /**
     * 与えられた日付と値の配列から、日付が連続しているかどうかを確認し、連続している場合は最新と前の値を返す関数
     *
     * @param array $dateArray 日付の配列
     * @param array $valueArray 値の配列
     *
     * @return array|false 連続している場合は最新と前の値を含む配列。`[$latestValue, $previousValue]`
     */
    private function areDatesConsecutive(array $dateArray, array $valueArray): array|false
    {
        // 最新の日付と前の日付を取得
        $latestDate = array_pop($dateArray);
        $previousDate = array_pop($dateArray);

        // 正規表現パターンを定義
        $datePattern = "/(\d{4}\/\d{2}\/\d{2})|(\d{2}\/\d{2})/";

        // 正規表現を使用して日付を抽出
        preg_match_all($datePattern, $latestDate, $latestDateMatches);
        preg_match_all($datePattern, $previousDate, $previousDateMatches);

        // 日付部分を取得
        $latestDatePart = $latestDateMatches[0][0];
        $previousDatePart = $previousDateMatches[0][0];

        // 日付が正しい形式でない場合は連続していないとみなす
        if (strtotime($latestDatePart) === false || strtotime($previousDatePart) === false) {
            return false;
        }

        // 日付の差を計算 (秒)
        $dateDifference = abs(strtotime($latestDatePart) - strtotime($previousDatePart));

        // 日付が1日（86400秒）の差でない場合は連続していないとみなす
        if ($dateDifference !== 86400) {
            return false;
        }

        // 連続している場合、最新と前の値を返す
        $latestValue = end($valueArray);
        $previousValue = prev($valueArray);

        return [$latestValue, $previousValue];
    }

    /**
     * 与えられた日付と値の配列から、1週間前の日付に対応する値を返す関数。
     *
     * @param array $dateArray 日付の配列
     * @param array $valueArray 値の配列
     *
     * @return mixed|false 1週間前の日付に対応する値。該当する日付がない場合は false。
     */
    private function findOneWeekBefore(array $dateArray, array $valueArray): mixed
    {
        // 最新の日付を取得
        $latestDate = end($dateArray);

        // 最新の日付がない場合は false を返す
        if (!$latestDate) {
            return false;
        }

        // 1週間前の日付を計算
        $oneWeekBefore = date('m/d', strtotime($latestDate . ' -1 week'));

        $year = date('Y');

        // 1週間前の日付が最新の日付よりも後の場合、前年の日付として扱う
        if (strtotime($oneWeekBefore) > strtotime($latestDate)) {
            $oneWeekBefore = ($year - 1) . '/' . $oneWeekBefore;
        }

        // 1週間前の日付が日付の配列に含まれているか確認
        if (in_array($oneWeekBefore, $dateArray)) {
            $key = array_search($oneWeekBefore, $dateArray);
            return $valueArray[$key];
        }

        // 該当する日付がない場合は false を返す
        return false;
    }

    /**
     * 最新の数値と前の数値から差分と増加率を計算して返す関数。
     *
     * @param int $latestNum 最新の数値
     * @param int $previousNum 前の数値
     *
     * @return array 連想配列で差分（'diffNum'）と増加率（'percentIncrease'）を含む配列。
     */
    private function calculateDiff(int $latestNum, int $previousNum): array
    {
        // 差分を計算
        $diffNum = $latestNum - $previousNum;

        // 増加率を計算
        $percentIncrease = ($diffNum / $previousNum * 100);

        // 小数点以下6桁までの精度で増加率を丸める
        $percentIncrease = floor($percentIncrease * 1000000) / 1000000;

        // 結果を連想配列で返す
        return compact('diffNum', 'percentIncrease');
    }
}
