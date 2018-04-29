#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""Slats
Main module
"""

import json
import shutil
from slatser import Slatser
from utils import write_file_json
from database import Database
import constants


def make_stats(slack):
    """ Make statistics """
    slack.download_history(constants.TMPDIR)
    emojis = slack.count_all_emojis(constants.TMPDIR)
    urls = slack.emoji.list().body['emoji']
    dbh = Database()

    data = []
    for name, count in zip(emojis.keys(), emojis.values()):
        url = urls[name]
        if 'alias' in url:
            url = ''
        data.append([count, name, url])
        dbh.new_emoji(name, url)
        dbh.new_count(name, count)
    return sorted(data, reverse=True)


def main(args):
    """ Main """
    slack = Slatser(args.token)
    data = make_stats(slack)

    if args.pretty:
        print(json.dumps(data, indent=2))
    if args.output:
        write_file_json(args.output, data)
    shutil.rmtree(constants.TMPDIR)
