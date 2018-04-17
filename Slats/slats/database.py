#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import sqlite3

PACKAGE_DIR = os.path.dirname(os.path.abspath(__file__))


class Database:
    """
    Interaction with the sqlite3 database
    """
    def __init__(self):
        dbfile = PACKAGE_DIR + '/../../var/resources/emoji.db'
        self.conn = sqlite3.connect(dbfile)
        self.curr = self.conn.cursor()
        self.__init()

    def __get_all_emojis_data(self):
        return self.curr.execute("""
            SELECT
                e.name as name,
                e.url as url,
                group_concat(c.count) as count
            FROM
                emoji AS e,
                count AS c
            WHERE e.id = c.id
            GROUP BY e.name
        """)

    def emojis(self, limit=168):
        """
        Get all the data about all the emojis
        """
        for _row in self.__get_all_emojis_data():
            yield (
                _row[0],
                _row[1],
                [int(i) for i in _row[2].split(',')][:limit]
            )

    def __init_emoji_table(self):
        self.curr.execute("""
            CREATE TABLE IF NOT EXISTS emoji (
                id    integer primary key autoincrement,
                name  varchar (64) unique not null,
                url   varchar (512) not null
            )
        """)

    def __init_count_table(self):
        self.curr.execute("""
            CREATE TABLE IF NOT EXISTS count (
                id    integer not null,
                count integer not null,
                date  datetime default current_timestamp,
                foreign key (id) references emoji(id)
            )
        """)

    def __init(self):
        """
        Initialize and make sure the database file has the right
        tables with the right colomns
        """
        self.__init_emoji_table()
        self.__init_count_table()

    def new_emoji(self, name, url=''):
        """
        Create a new record of a new emoji
        """
        self.curr.execute(
            "INSERT OR IGNORE INTO emoji (name, url) VALUES (?, ?)", (name, url)
        )
        self.conn.commit()

    def new_count(self, name, count=0):
        """
        Make a new datapoint
        """
        self.curr.execute("""
            INSERT OR IGNORE INTO count (id, count) VALUES
                ((SELECT id FROM emoji WHERE name = ?), ?)
            """,
            (name, count)
        )
        self.conn.commit()



def testing():
    dbh = Database()
    # dbh.new_emoji('token')
    # dbh.new_count('token', 20)
    print([r for r in dbh.emojis()])

#testing()
