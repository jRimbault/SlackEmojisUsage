#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import json
from slatser import Slatser


TMPDIR = '/tmp/channels'


def make_stats(slack):
    totalMessages = {}
    for messages in slack.download_history(TMPDIR):
        totalMessages[messages[0]] = messages[1]
    totalEmojis = slack.count_all_emojis(TMPDIR)
    return {
        'total': {
            'messages': sum(totalMessages.values()),
            'emojis': sum(totalEmojis.values())
        },
        'channel': totalMessages,
        'emoji': totalEmojis,
    }


""" Main """
def main(args):
    stats = make_stats(Slatser(args.token))
    if args.pretty:
        print(json.dumps(stats, indent=2, sort_keys=True))
    else:
        print(json.dumps(stats))
