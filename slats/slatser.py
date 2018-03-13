#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slatser class
Extend Slacker to expose my own methods
"""

from slacker import Slacker
from utils import grep, mkdir, write_file_json


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
            yield {'id': channel['id'], 'name': channel['name']}


    def channel_full_history(self, channel, pagesize=100):
        """ Download all available messages from a channel """
        messages = []
        last_timestamp = None

        while True:
            response = self.channels.history(
                channel = channel,
                latest  = last_timestamp,
                oldest  = 0,
                count   = pagesize
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
        mkdir(path)
        for channel in self.channels_list():
            messages = self.channel_full_history(channel['id'])
            write_file_json(
                path + '/' + channel['name'] + '.json',
                messages
            )
            # yield (channel['name'], len(messages))


    def count_all_emojis(self, path):
        """ Count all occurences of all custom emojis """
        total = {}
        for emoji in self.emojis_list():
            total[emoji] = grep(':'+emoji+':', path)
        return total
