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
    id    integer not null,
    count integer not null,
    date  datetime default current_timestamp,
    foreign key (id) references emoji(id)
)
"""

INSERT_NEW_EMOJI = """
INSERT OR IGNORE INTO emoji (name, url) VALUES (?, ?)
"""

INSERT_NEW_COUNT = """
INSERT OR IGNORE INTO count (id, count) VALUES
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
WHERE e.id = c.id
GROUP BY e.name
"""
