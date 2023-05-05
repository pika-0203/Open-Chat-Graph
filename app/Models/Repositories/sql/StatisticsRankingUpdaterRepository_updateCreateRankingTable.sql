DELETE FROM
    statistics_ranking;

INSERT INTO
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
                    `date` DESC
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
            `date` >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            AND `date` <= (
                SELECT
                    DATE_SUB(MAX(`date`), INTERVAL 1 DAY)
                FROM
                    statistics
                WHERE
                    open_chat_id = statistics.open_chat_id
            )
            AND member >= 10
        GROUP BY
            open_chat_id
    ) t2 ON t1.open_chat_id = t2.open_chat_id
ORDER BY
    index1 DESC;

SET
    @row_number := 0;

UPDATE
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
    statistics_ranking.id = subquery.new_id;