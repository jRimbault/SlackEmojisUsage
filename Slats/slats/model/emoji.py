#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from .database import Database
from . import queries


DBH = Database()

class Emoji:
    """ Emoji """

    def __init__(self, name):
        self.__by_name(name)

    def __by_name(self, name, limit=168):
        raw_data = DBH.curr.execute(
            queries.EMOJI_BY_NAME,
            {'name': name, 'limit': limit}
        ).fetchone()
        self.name = raw_data[0]
        self.url = raw_data[1]
        self.counts = list(reversed([int(i) for i in raw_data[2].split(',')]))
        self.dates = list(reversed([date for date in raw_data[3].split(',')]))

    def __str__(self):
        return str(dict(self))

    def __iter__(self):
        yield 'name', self.name
        yield 'url', self.url
        yield 'data', [self.counts, self.dates]

    def sum(self):
        return sum(self.counts)

    @staticmethod
    def names():
        names = Database().curr.execute(queries.EMOJI_NAMES)
        for name in names.fetchall():
            yield name[0]

    @staticmethod
    def all():
        for name in Emoji.names():
            yield Emoji(name=name)

    @staticmethod
    def all_sorted():
        emojis = []
        for emoji in Emoji.all():
            emojis.append(emoji)
        return sorted(emojis, key=lambda e: e.sum())

    @staticmethod
    def new(name, url=''):
        DBH.curr.execute(queries.INSERT_NEW_EMOJI, (name, url))
        DBH.conn.commit()

    @staticmethod
    def count(name):
        return Count(name)


class Count:
    """ Count """
    def __init__(self, name):
        self.name = name

    def new(self, count):
        DBH.curr.execute(queries.INSERT_NEW_COUNT, (self.name, count))
        DBH.conn.commit()
