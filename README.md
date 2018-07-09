# Slats


Requirements:
- Python 3.6
- pip/pipenv


Usage:

```shell
pipenv shell
pipenv install
# for slats
python -m slack_emojis -h
# for django
python manage.py runserver
```

### Sqlite3 database

```sql
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
```

### Todo

Count using collection.Counter instead of calling grep.
