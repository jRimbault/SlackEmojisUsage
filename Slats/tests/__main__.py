#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from slats.model.emoji import Emoji


def main():
    import json
    print(json.dumps([dict(emoji) for emoji in Emoji.all_sorted()]))


if __name__ == '__main__':
    main()
