#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slats
Main module
"""

from datetime import datetime

class Stats(object):
    def __init__(self, slack):
        """ Make statistics """
        self.emojis = slack.total()
        self.urls = slack.emoji.list().body['emoji']

    def save(self):
        # todo : use django models
        for name, count in self.emojis.items():
            print(name, count, self.urls[name])
            url = self.urls[name]
            if 'alias' in url:
                url = ''
            save_count(save_emoji(name, url), count)


def save_emoji(name, url):
    from api.model import Emoji
    e, created = Emoji.objects.get_or_create(name=name, url=url)
    e = Emoji.objects.get(name=name, url=url)
    return e.id


def save_count(e_id, count):
    from api.model import Count
    Count.objects.create(
        count=count,
        date=datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
        fk_emoji_id=e_id
    )
