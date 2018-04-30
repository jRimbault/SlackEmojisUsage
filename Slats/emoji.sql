-- tables

CREATE TABLE IF NOT EXISTS emoji (
    id    integer primary key autoincrement,
    name  varchar (64) unique not null,
    url   varchar (512) not null
);

CREATE TABLE IF NOT EXISTS count (
    id    integer not null,
    count integer not null,
    date  datetime default current_timestamp,
    foreign key (id) references emoji(id)
);
