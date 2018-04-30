#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from .database import Database
from . import queries


class Emoji:
    """ Emoji """

    def __init__(self, name):
        self.dbh = Database()
        self.__by_name(name)

    def __by_name(self, name, limit=168):
        raw_data = self.dbh.curr.execute(
            queries.EMOJI_BY_NAME,
            {'name': name, 'limit': limit}
        ).fetchone()
        self.name = raw_data[0]
        self.url = raw_data[1]
        self.count = list(reversed([int(i) for i in raw_data[2].split(',')]))
        self.dates = list(reversed([date for date in raw_data[3].split(',')]))

    def __str__(self):
        return str(dict(self))

    def __iter__(self):
        yield 'name', self.name
        yield 'url', self.url
        yield 'data', [self.count, self.dates]

    def sum(self):
        return sum(self.count)

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
