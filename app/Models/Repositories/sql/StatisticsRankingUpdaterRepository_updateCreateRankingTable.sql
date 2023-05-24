/*
 * 昨日のメンバー数と、最新メンバー数を比較して、差と増減%をランキングテーブルに挿入する。
 * 差 + (増減% * 5) を`index1`カラムに挿入する。
 * メンバー１０人以上のオープンチャットが対象
 * 増加率の重みを下げ、大規模なOCの上位表示を重視しました。
 * 急な増加(増加率 >= 1.75)には重み(増加率 * 7.5)が付きます。
 *　これによって、ランキングが不安定な物になるのを防ぎ、小規模のOCでも上位に上がれるように調整しました。
 */
INSERT INTO
    statistics_ranking_temp (
        open_chat_id,
        diff_member,
        percent_increase,
        index1
    )
SELECT
    t1.open_chat_id,
    t1.member - t2.member AS diff_member,
    ((t1.member - t2.member) / t2.member) * 100 AS percent_increase,
    (
        (t1.member - t2.member) + (
            CASE WHEN ((t1.member - t2.member) / t2.member) > 1.75 THEN (((t1.member - t2.member) / t2.member) * 7.5)
                 ELSE (((t1.member - t2.member) / t2.member) * 5)
            END
        )
    ) AS index1
FROM
    (
        SELECT
            st.open_chat_id,
            (
                SELECT
                    st2.member
                FROM
                    statistics AS st2
                WHERE
                    st2.open_chat_id = st.open_chat_id
                ORDER BY
                    `date` DESC
                LIMIT
                    1
            ) AS member
        FROM
            statistics AS st
        WHERE
            st.member >= 10
        GROUP BY
            st.open_chat_id
    ) t1
    JOIN (
        SELECT
            st3.open_chat_id,
            AVG(st3.member) AS member
        FROM
            statistics AS st3
        WHERE
            `date` = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            AND st3.member >= 10
        GROUP BY
            st3.open_chat_id
    ) t2 ON t1.open_chat_id = t2.open_chat_id;

/*
 * `index1`カラムを降順でソートして、statistics_rankingに挿入する。
 */
DELETE FROM
    statistics_ranking;

SET
    @row_number := 0;

INSERT INTO
    statistics_ranking (
        id,
        open_chat_id,
        diff_member,
        percent_increase,
        index1
    )
SELECT
    (@row_number := @row_number + 1),
    statistics_ranking_temp.*
FROM
    statistics_ranking_temp
ORDER BY
    CASE
        WHEN index1 > 0 THEN 1 -- index1が0より上のグループ
        WHEN index1 = 0 THEN 2 -- index1が0のグループ
        ELSE 3 -- index1が0未満のグループ
    END,
    CASE
        WHEN index1 = 0 THEN open_chat_id -- open_chat_idで降順ソート
        ELSE index1
    END DESC;

DELETE FROM
    statistics_ranking_temp;
