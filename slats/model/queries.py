#!/usr/bin/env python3
# -*- coding: utf-8 -*-


INIT_TABLE_EMOJI = """
CREATE TABLE IF NOT EXISTS emoji (
    id    integer primary key autoincrement,
    name  varchar (64) unique not null,
    url   varchar (512) not null
)
"""

INIT_TABLE_COUNT = """
CREATE TABLE IF NOT EXISTS count (
    id           integer primary key autoincrement,
    fk_emoji_id  integer not null,
    count        integer not null,
    date         datetime default current_timestamp,
    foreign key (fk_emoji_id) references emoji(id)
);
"""

INSERT_NEW_EMOJI = """
INSERT OR IGNORE INTO emoji (name, url) VALUES (?, ?)
"""

INSERT_NEW_COUNT = """
INSERT OR IGNORE INTO count (fk_emoji_id, count) VALUES
    ((SELECT id FROM emoji WHERE name = ?), ?)
"""

SELECT_ALL = """
SELECT
    e.name as name,
    e.url as url,
    group_concat(c.count) as count
FROM
    emoji AS e,
    count AS c
WHERE e.id = c.fk_emoji_id
GROUP BY e.name
"""

EMOJI_BY_NAME = """
SELECT
    e.name AS name,
    e.url AS url,
    group_concat(count) AS count,
    group_concat(date) as date
FROM emoji AS e, (
    SELECT count, date
    FROM count
    WHERE fk_emoji_id = (
        SELECT id
        FROM emoji
        WHERE name = :name
    )
    ORDER BY date DESC
    LIMIT :limit
)
WHERE e.name = :name
"""

EMOJI_NAMES = "SELECT name FROM emoji"
