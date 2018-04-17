<?php


return [
    // get all names
    'names' => 'SELECT name from emoji',
    // get all
    'general' => 'SELECT e.name, e.url, group_concat(c.count)
                  FROM emoji AS e, count AS c
                  WHERE e.id = c.id
                  GROUP BY e.names',
    // get data about a single emoji
    'emoji' => 'SELECT
                    name,
                    url, (
                        SELECT group_concat(count)
                        FROM (
                            SELECT count
                            FROM count
                            WHERE id = (
                                SELECT id
                                FROM emoji
                                WHERE name = ?
                            )
                            ORDER BY date ASC
                        )
                    ) AS count
                FROM emoji
                WHERE name = ?',
];
