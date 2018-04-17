-- tables

create table emoji (
    id    integer primary key autoincrement,
    name  varchar (64) unique not null,
    url   varchar (512) not null
);

create table count (
    id    integer not null,
    count integer not null,
    date  datetime default current_timestamp,
    foreign key (id) references emoji(id)
);

-- test data

insert into emoji (name, url) values
    ('sad', 'http://sad.com'),
    ('happy', 'http://happy.com'),
    ('renifle', 'http://renifle.com'),
    ('apart', 'http://apart.com');

-- pause between inserts

insert into count (id, count) values
    ((select id from emoji where name = 'sad'), 15),
    ((select id from emoji where name = 'happy'), 17),
    ((select id from emoji where name = 'renifle'), 24),
    ((select id from emoji where name = 'apart'), 13);

-- pause between inserts

insert into count (id, count) values
    ((select id from emoji where name = 'sad'), 13),
    ((select id from emoji where name = 'happy'), 25),
    ((select id from emoji where name = 'renifle'), 19),
    ((select id from emoji where name = 'apart'), 22);

-- pause between inserts

insert into count (id, count) values
    ((select id from emoji where name = 'sad'), 14),
    ((select id from emoji where name = 'happy'), 18),
    ((select id from emoji where name = 'renifle'), 22),
    ((select id from emoji where name = 'apart'), 16);

-- pause between inserts

insert into count (id, count) values
    ((select id from emoji where name = 'sad'), 8),
    ((select id from emoji where name = 'happy'), 30),
    ((select id from emoji where name = 'renifle'), 17),
    ((select id from emoji where name = 'apart'), 19);

-- test selection

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
    e.name as name,
    e.url as url,
    group_concat(count) as count
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


-- insert

INSERT OR IGNORE INTO emoji (name, url) VALUES
    ('saddest', 'http://sad.com');
