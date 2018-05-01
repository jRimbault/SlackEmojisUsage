#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slats
Main module
"""

import json
import shutil
from .slatser import Slatser
from .emoji import Emoji
from . import constants


def make_stats(slack):
    """ Make statistics """
    slack.download_history(constants.TMPDIR)
    emojis = slack.count_all_emojis(constants.TMPDIR)
    urls = slack.emoji.list().body['emoji']

    data = []
    for name, count in zip(emojis.keys(), emojis.values()):
        url = urls[name]
        if 'alias' in url:
            url = ''
        data.append([count, name, url])
        Emoji.new(name, url)
        Emoji.newcount(name, count)
    return sorted(data, reverse=True)


def main(args):
    """ Main """
    data = make_stats(Slatser(args.token))

    if args.pretty:
        print(json.dumps(data, indent=2))
    if args.output:
        print(
            json.dumps(data),
            file=open(args.output, mode='w')
        )
    shutil.rmtree(constants.TMPDIR)
