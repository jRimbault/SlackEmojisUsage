-- tables

CREATE TABLE IF NOT EXISTS emoji (
    id    integer primary key autoincrement,
    name  varchar (64) unique not null,
    url   varchar (512) not null
);

CREATE TABLE IF NOT EXISTS count (
    id           integer primary key autoincrement,
    fk_emoji_id  integer not null,
    count        integer not null,
    date         datetime default current_timestamp,
    foreign key (fk_emoji_id) references emoji(id)
);

BEGIN TRANSACTION;

ALTER TABLE count RENAME TO _count_old;

CREATE TABLE IF NOT EXISTS count (
    id           integer primary key autoincrement,
    fk_emoji_id  integer not null,
    count        integer not null,
    date         datetime default current_timestamp,
    foreign key (fk_emoji_id) references emoji(id)
);

INSERT INTO count (fk_emoji_id, count, date)
  SELECT id, count, date
  FROM _count_old;

COMMIT;

PRAGMA foreign_keys=on;
