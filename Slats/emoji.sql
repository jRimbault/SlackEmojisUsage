-- tables

CREATE TABLE emoji (
    id    integer primary key autoincrement,
    name  varchar (64) unique not null,
    url   varchar (512) not null
);

CREATE TABLE count (
    id    integer not null,
    count integer not null,
    date  datetime default current_timestamp,
    foreign key (id) references emoji(id)
);

-- test data

INSERT OR IGNORE INTO emoji (name, url) VALUES
    ('sad', 'http://sad.com'),
    ('happy', 'http://happy.com'),
    ('renifle', 'http://renifle.com'),
    ('apart', 'http://apart.com');

-- pause between inserts

INSERT INTO count (id, count) VALUES
    ((SELECT id FROM emoji WHERE name = 'sad'), 15),
    ((SELECT id FROM emoji WHERE name = 'happy'), 17),
    ((SELECT id FROM emoji WHERE name = 'renifle'), 24),
    ((SELECT id FROM emoji WHERE name = 'apart'), 13);

-- pause between inserts

INSERT INTO count (id, count) VALUES
    ((SELECT id FROM emoji WHERE name = 'sad'), 13),
    ((SELECT id FROM emoji WHERE name = 'happy'), 25),
    ((SELECT id FROM emoji WHERE name = 'renifle'), 19),
    ((SELECT id FROM emoji WHERE name = 'apart'), 22);

-- pause between inserts

INSERT INTO count (id, count) VALUES
    ((SELECT id FROM emoji WHERE name = 'sad'), 14),
    ((SELECT id FROM emoji WHERE name = 'happy'), 18),
    ((SELECT id FROM emoji WHERE name = 'renifle'), 22),
    ((SELECT id FROM emoji WHERE name = 'apart'), 16);

-- pause between inserts

INSERT INTO count (id, count) VALUES
    ((SELECT id FROM emoji WHERE name = 'sad'), 8),
    ((SELECT id FROM emoji WHERE name = 'happy'), 30),
    ((SELECT id FROM emoji WHERE name = 'renifle'), 17),
    ((SELECT id FROM emoji WHERE name = 'apart'), 19);

-- test selection

SELECT e.name, e.url, group_concat(c.count)
FROM emoji AS e, count AS c
WHERE e.id = c.id
GROUP BY e.name;

SELECT
    name,
    url, (
        SELECT group_concat(count)
        FROM (
            SELECT count
            FROM count
            WHERE id = (
                SELECT id
                FROM emoji
                WHERE name = 'sad'
            )
            ORDER BY date ASC
        )
    ) AS count
FROM emoji
WHERE name = 'sad';

SELECT
    e.name AS name,
    e.url AS url,
    group_concat(count) AS count
FROM emoji AS e, (
    SELECT count
    FROM count
    WHERE id = (
        SELECT id
        FROM emoji
        WHERE name = 'sad'
    )
    ORDER BY date ASC
)
WHERE e.name = 'sad';
