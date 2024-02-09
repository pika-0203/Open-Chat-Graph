DROP TABLE IF EXISTS statistics_ranking_week_temp;

CREATE TABLE statistics_ranking_week_temp (
    open_chat_id INTEGER,
    diff_member INTEGER,
    percent_increase REAL,
    index1 REAL
);

INSERT INTO
    statistics_ranking_week_temp
SELECT
    t1.open_chat_id,
    t1.member - t2.member AS diff_member,
    (
        (
            CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
        ) * 100.0 / CAST(t2.member AS REAL)
    ) AS percent_increase,
    (
        CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
    ) + (
        (
            (
                CAST(t1.member AS REAL) - CAST(t2.member AS REAL)
            ) / CAST(t2.member AS REAL)
        ) * 20
    ) AS index1
FROM
    (
        SELECT
            st.*
        FROM
            statistics AS st
        WHERE
            date = date(':DATE_STRING')
            AND member >= 10
            AND EXISTS (
                SELECT
                    *
                FROM
                    statistics AS sub
                WHERE
                    sub.open_chat_id = st.open_chat_id
                    AND date = date(':DATE_STRING', '-1 day')
            )
    ) t1
    JOIN (
        SELECT
            *
        FROM
            statistics
        WHERE
            date = date(':DATE_STRING', '-7 day')
            AND member >= 10
    ) t2 ON t1.open_chat_id = t2.open_chat_id;

DELETE FROM
    statistics_ranking_week;

INSERT INTO
    statistics_ranking_week
SELECT
    rowid,  
    open_chat_id,
    diff_member,
    percent_increase
FROM
    (
        SELECT
            *
        FROM
            statistics_ranking_week_temp
        ORDER BY
            CASE
                WHEN index1 > 0 THEN 1
                WHEN index1 = 0 THEN 2
                ELSE 3
            END,
            CASE
                WHEN index1 = 0 THEN (
                    SELECT
                        member
                    FROM
                        statistics
                    WHERE
                        open_chat_id = statistics_ranking_week_temp.open_chat_id
                    ORDER BY
                        id DESC
                    LIMIT
                        1
                )
                ELSE index1
            END DESC

);

DROP TABLE statistics_ranking_week_temp;