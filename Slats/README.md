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

Json data structure:

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

### Todo:

Instead of making only snapshots, I should store the data in a DB of some sort. Sqlite if I only want the last week for example.

Database example:
```sql
create table emoji (
    id    integer primary key autoincrement,
    name  text unique not null,
    url   text not null
);
create table count (
    id    integer not null,
    count integer not null,
    date  datetime default current_timestamp,
    foreign key (id) references emoji(id)
);
```

Requests examples:
```sql
-- sqlite3 doesn't allow ordering the group_concat
-- this would return the desired result only coincidentally
select e.name, e.url, group_concat(c.count)
from emoji as e, count as c
where e.id = c.id
group by e.name;

-- this returns the desired values, 
-- but only for one emoji at a time
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
        WHERE name = ?
    )
    ORDER BY date ASC
)
WHERE e.name = ?;
```
