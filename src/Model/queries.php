<?php


return [
    // get all names
    'names' => 'SELECT name from emoji',
    // get all
    'general' => 'SELECT
                      e.name as name,
                      e.url as url,
                      group_concat(c.count) as count
                  FROM
                      emoji AS e,
                      count AS c
                  WHERE e.id = c.id
                  GROUP BY e.name',
    // get data about a single emoji
    'emoji' => 'SELECT
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
                WHERE e.name = ?',
];
