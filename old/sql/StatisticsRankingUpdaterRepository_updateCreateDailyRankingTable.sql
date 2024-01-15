/*
 * 前日のメンバー数と本日のメンバー数を比較して、差と増減%をランキングテーブルに挿入する。
 * 差 + (増減% / 10) を`index1`カラムに挿入する。
 * メンバー１０人以上のオープンチャットが対象
 */
INSERT INTO
    statistics_ranking_day_temp (
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
        (t1.member - t2.member) + (((t1.member - t2.member) / t2.member) * 20)
    ) AS index1
FROM
    (
        SELECT
            st.*
        FROM
            statistics as st
        WHERE
            st.date = CURDATE()
            AND st.member >= 10
    ) t1
    JOIN (
        SELECT
            st3.*
        FROM
            statistics AS st3
        WHERE
            st3.date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            AND st3.member >= 10
    ) t2 ON t1.open_chat_id = t2.open_chat_id;

/*
 * `index1`カラムを降順でソートして、statistics_rankingに挿入する。
 */
DELETE FROM
    statistics_ranking_day;

SET
    @row_number := 0;

INSERT INTO
    statistics_ranking_day (
        id,
        open_chat_id,
        diff_member,
        percent_increase
    )
SELECT
    (@row_number := @row_number + 1),
    statistics_ranking_day_temp.open_chat_id,
    statistics_ranking_day_temp.diff_member,
    statistics_ranking_day_temp.percent_increase
FROM
    statistics_ranking_day_temp
ORDER BY
    CASE
        WHEN index1 > 0 THEN 1 -- index1が0より上のグループ
        WHEN index1 = 0 THEN 2 -- index1が0のグループ
        ELSE 3 -- index1が0未満のグループ
    END,
    CASE
        WHEN index1 = 0 THEN (
            SELECT
                member
            FROM
                open_chat
            WHERE
                id = statistics_ranking_day_temp.open_chat_id
        ) -- memberで降順ソート
        ELSE index1
    END DESC;

DELETE FROM
    statistics_ranking_day_temp;