#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sqlite3
from . import queries
from . import constants


class Database:
    """
    Interaction with the sqlite3 database
    """
    def __init__(self, path='/../../var/resources/api.db'):
        dbfile = constants.PACKAGE_DIR + path
        self.conn = sqlite3.connect(dbfile)
        self.curr = self.conn.cursor()
        self.__init_emoji_table()
        self.__init_count_table()

    def __init_emoji_table(self):
        self.curr.execute(queries.INIT_TABLE_EMOJI)
        self.conn.commit()

    def __init_count_table(self):
        self.curr.execute(queries.INIT_TABLE_COUNT)
        self.conn.commit()

    def __get_all_emojis_data(self):
        return self.curr.execute(queries.SELECT_ALL)

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

    def new_emoji(self, name, url=''):
        """
        Create a new record of a new emoji
        """
        self.curr.execute(queries.INSERT_NEW_EMOJI, (name, url))
        self.conn.commit()

    def new_count(self, name, count=0):
        """
        Make a new datapoint
        """
        self.curr.execute(queries.INSERT_NEW_COUNT, (name, count))
        self.conn.commit()
