#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from slacker import Slacker
from utils import grep, mkdir, write_file_json


class Slatser(Slacker):

    """ Parse Slack's API response to get only the list of Emojis """
    def emojis_list(self):
        emojis = self.emoji.list().body['emoji']
        for emoji in emojis:
            yield emoji


    """ Get all the public channels from Slack """
    def channels_list(self):
        channels = self.channels.list(exclude_archived=True).body['channels']
        for channel in channels:
            yield {'id': channel['id'], 'name': channel['name']}


    """ Download all available messages from a channel """
    def channel_full_history(self, channel, pagesize = 100):
        messages = []
        lastTimestamp = None

        while True:
            response = self.channels.history(
                channel = channel,
                latest  = lastTimestamp,
                oldest  = 0,
                count   = pagesize
            ).body

            messages.extend(response['messages'])

            if response['has_more'] == True:
                lastTimestamp = messages[-1]['ts'] # -1 last element of list
            else:
                break

        return messages


    """
    Dump all channels's messages into distinct files
    yield their names and messages count
    """
    def download_history(self, path):
        mkdir(path)
        for channel in self.channels_list():
            messages = self.channel_full_history(channel['id'])
            write_file_json(
                path + '/' + channel['name'] + '.json',
                messages
            )
            yield (channel['name'], len(messages))


    """ Count all occurences of all custom emojis """
    def count_all_emojis(self, path):
        total = {}
        for emoji in self.emojis_list():
            total[emoji] = grep(':'+emoji+':', path)
        return total

