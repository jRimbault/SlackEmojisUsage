<?php
/**
 * Keep all SQL queries here
 */

// get all emojis' names
$names = 'SELECT name from emoji';

// get all, without count order sequence guarantee
$general = <<<'SQL'
SELECT
    e.name AS name,
    e.url AS url,
    group_concat(c.count) AS count
FROM
    emoji AS e,
    count AS c
WHERE e.id = c.id
GROUP BY e.name
SQL;

// get data about a single emoji, count order sequence guaranteed
$emoji = <<<'SQL'
SELECT
    e.name AS name,
    e.url AS url,
    group_concat(count) AS count,
    group_concat(date) as date
FROM emoji AS e, (
    SELECT count, date
    FROM count
    WHERE id = (
        SELECT id
        FROM emoji
        WHERE name = :name
    )
    ORDER BY date DESC
    LIMIT :limit
)
WHERE e.name = :name
SQL;


return [
    'names' => $names,
    'general' => $general,
    'emoji' => $emoji,
];
