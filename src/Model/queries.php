<?php

// get all emojis' names
$getAllNames =
    'SELECT name from emoji';

// get all, without count order sequence guarantee
$getAllEmoji =
    'SELECT
        e.name as name,
        e.url as url,
        group_concat(c.count) as count
    FROM
        emoji AS e,
        count AS c
    WHERE e.id = c.id
    GROUP BY e.name';

// get data about a single emoji, count order sequence guarenteed
$selectEmoji =
    'SELECT
        e.name as name,
        e.url as url,
        group_concat(count) as count
    FROM emoji AS e, (
        SELECT count
        FROM count
        WHERE id = (
            SELECT id
            FROM emoji
            WHERE name = ?
        )
        ORDER BY date ASC
    )
    WHERE e.name = ?';


return [
    'names' => $getAllNames,
    'general' => $getAllEmoji,
    'emoji' => $selectEmoji,
];
