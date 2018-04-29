# Slats


Requirements:
- Python 3.5
- pip/pipenv


Usage:

```shell
pipenv shell
pipenv install
python slats -h
```

### Json snapshot

```json
[
    [
        <number of occurences>,
        "emoji name",
        "emoji url"
    ],
    [...],
    [...]
    ...
]
```
Array of arrays containing each 3 values:
- the custom emoji's number of occurences
- its name
- its URL

### Sqlite3 database

```sql
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
```
