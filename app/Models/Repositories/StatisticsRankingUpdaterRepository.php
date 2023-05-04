<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;

class StatisticsRankingUpdaterRepository implements StatisticsRankingUpdaterRepositoryInterface
{
    public function updateCreateRankingTable(int $numberRecords): void
    {
        DB::transaction(function () use ($numberRecords) {
            $this->updateCreate($numberRecords);
        });
    }

    private function updateCreate(int $numberRecords): void
    {
        DB::$pdo->exec(
            'DELETE FROM statistics_ranking'
        );

        /**
         *  メンバー１０人以上のオープンチャットが対象 
         *  直近１週間の低いメンバー数と、現在のメンバー数の差を比較して、増減%と差分の人数を取得する。
         *  差分の人数 + (増減% / 10) を指数 `index1` として並び順を降順にソートする。
         */
        DB::execute(
            'INSERT INTO
                statistics_ranking (
                    id,
                    open_chat_id,
                    diff_member,
                    percent_increase,
                    index1
                )
            SELECT
                t1.open_chat_id,
                t1.open_chat_id,
                t1.member - t2.member as diff_member,
                ((t1.member - t2.member) / t2.member) * 100 as percent_increase,
                (
                    (t1.member - t2.member) + (((t1.member - t2.member) / t2.member) * 10)
                ) as index1
            FROM
                (
                    SELECT
                        st.open_chat_id,
                        (
                            SELECT
                                member
                            FROM
                                statistics
                            WHERE
                                open_chat_id = st.open_chat_id
                            ORDER BY
                                date DESC
                            LIMIT
                                1
                        ) AS member
                    FROM
                        statistics as st
                    WHERE
                        st.member >= 10
                    GROUP BY
                        st.open_chat_id
                ) t1
                LEFT JOIN (
                    SELECT
                        open_chat_id,
                        MIN(member) as member
                    FROM
                        statistics
                    WHERE
                        date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        AND member >= 10
                    GROUP BY
                        open_chat_id
                ) t2 ON t1.open_chat_id = t2.open_chat_id
            ORDER BY
                index1 DESC
            LIMIT
                :numberRecords;',
            ['numberRecords' => $numberRecords]
        );

        DB::$pdo->exec(
            'SET @row_number := 0;'
        );

        DB::$pdo->exec(
            'UPDATE
                statistics_ranking
                JOIN (
                    SELECT
                        id,
                        (@row_number := @row_number + 1) AS new_id
                    FROM
                        statistics_ranking
                    ORDER BY
                        index1 DESC
                ) subquery ON statistics_ranking.id = subquery.id
            SET
                statistics_ranking.id = subquery.new_id;'
        );
    }
}
