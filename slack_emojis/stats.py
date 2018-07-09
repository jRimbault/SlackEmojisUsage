#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slats
Main module
"""

from slack_emojis.model import Emoji


class Stats(object):
    def __init__(self, slack, tmp_dir):
        """ Make statistics """
        slack.download_history(tmp_dir)
        self.emojis = slack.count_all_emojis(tmp_dir)
        self.urls = slack.emoji.list().body['emoji']

    def save(self):
        # todo : use django models
        for name, count in self.emojis.items():
            print(name, count, self.urls[name])
            url = self.urls[name]
            if 'alias' in url:
                url = ''
            Emoji.new(name, url)
            Emoji.count(name).new(count)
