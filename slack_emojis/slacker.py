#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slatser class
Extend Slacker to expose my own methods
"""

import json
import subprocess
from collections import Counter
from pathlib import Path
from slacker import Slacker as SlackerOrigin


class Slacker(SlackerOrigin):
    """Slatser class
    Extend Slacker to expose my own methods
    """

    def emojis_list(self):
        """ Parse Slack's API response to get only the list of Emojis """
        for emoji in self.emoji.list().body['emoji']:
            yield emoji


    def channels_list(self):
        """ Get all the public channels from Slack """
        for channel in self.channels.list(exclude_archived=True).body['channels']:
            yield channel['id']

    def total(self):
        messages = []
        for c in self.channels_list():
            messages.extend(self.messages(c))
        return self.counter(messages)

    def counter(self, messages):
        count = Counter(messages)
        return {e: count[':' + e + ':'] for e in self.emojis_list()}

    def messages(self, channel):
        messages = []
        last_timestamp = None

        while True:
            response = self.channels.history(
                channel=channel,
                latest=last_timestamp,
                oldest=0,
                count=100
            ).body

            messages.extend([m['text'] for m in response['messages']])

            if not response['has_more']:
                break

            last_timestamp = response['messages'][-1]['ts']

        return messages


def spinner():
    while True:
        for cursor in '⣾⣽⣻⢿⡿⣟⣯⣷':
            yield cursor
