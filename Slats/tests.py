#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from slats.emoji import Emoji


def get_all_emojis():
    import json
    print(json.dumps([dict(emoji) for emoji in Emoji.all_sorted()]))


get_all_emojis()
