#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slatser class
Extend Slacker to expose my own methods
"""

import json
from slacker import Slacker
from .utils import grep


class Slatser(Slacker):
    """Slatser class
    Extend Slacker to expose my own methods
    """

    def emojis_list(self):
        """ Parse Slack's API response to get only the list of Emojis """
        emojis = self.emoji.list().body['emoji']
        for emoji in emojis:
            yield emoji


    def channels_list(self):
        """ Get all the public channels from Slack """
        channels = self.channels.list(exclude_archived=True).body['channels']
        for channel in channels:
            yield channel['id']


    def channel_full_history(self, channel, pagesize=100):
        """ Download all available messages from a channel """
        messages = []
        last_timestamp = None

        cursor = spinner()
        while True:
            print("Loading", next(cursor), end='\r')
            response = self.channels.history(
                channel=channel,
                latest=last_timestamp,
                oldest=0,
                count=pagesize
            ).body

            messages.extend(response['messages'])

            if not response['has_more']:
                break

            last_timestamp = messages[-1]['ts'] # -1 last element of list

        return messages


    def download_history(self, path):
        """
        Dump all channels's messages into distinct files
        yield their names and messages count
        """
        for channel_id in self.channels_list():
            messages = self.channel_full_history(channel_id)
            print(
                json.dumps(messages),
                file=open(path + '/' + channel_id + '.json', mode='w')
            )


    def count_all_emojis(self, path):
        """ Count all occurences of all custom emojis """
        total = {}
        for emoji in self.emojis_list():
            total[emoji] = grep(':'+emoji+':', path)
        return total


def spinner():
    while True:
        for cursor in '⣾⣽⣻⢿⡿⣟⣯⣷':
            yield cursor
