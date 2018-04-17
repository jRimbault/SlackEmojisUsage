#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import argparse
from main import main


if __name__ == '__main__':
    parser = argparse.ArgumentParser(
        description='''
Count occurences of each custom emoji in Slack
''',
        formatter_class=argparse.RawTextHelpFormatter
    )
    parser.add_argument('-d', '--directory', help='Where to store the data')
    parser.add_argument('token', help='Slack API token')
    group_action = parser.add_mutually_exclusive_group()
    group_action.add_argument(
        '-p', '--pretty',
        help='Pretty print JSON output',
        action='store_true'
    )
    group_action.add_argument(
        '-o', '--output',
        help='Output file'
    )
    main(parser.parse_args())
