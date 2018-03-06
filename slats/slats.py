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
        'totals': {
            'messages': sum(totalMessages.values()),
            'emojis': sum(totalEmojis.values())
        },
        'channels': totalMessages,
        'emojis': totalEmojis,
    }


""" Main """
def main(args):
    slack = Slatser(args.token)
    print(json.dumps(make_stats(slack), indent=2, sort_keys=True))
