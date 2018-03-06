#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slats
Main module
"""

import json
from slatser import Slatser
from utils import write_file_json

TMPDIR = '/tmp/channels'


def make_stats(slack):
    """ Make statistics """
    total_messages = {}
    for messages in slack.download_history(TMPDIR):
        total_messages[messages[0]] = messages[1]
    total_emojis = slack.count_all_emojis(TMPDIR)
    return {
        'total': {
            'messages': sum(total_messages.values()),
            'emojis': sum(total_emojis.values())
        },
        'channel': total_messages,
        'emoji': total_emojis,
        'urls': slack.emoji.list().body['emoji']
    }


def main(args):
    """ Main """
    stats = make_stats(Slatser(args.token))

    if args.pretty:
        print(json.dumps(stats, indent=2, sort_keys=True))
        return

    if args.output:
        write_file_json(args.output, stats)
    else:
        print(json.dumps(stats))
