
import shutil
import sys
import argparse
from slack_emojis import settings
from slack_emojis import Stats
from slack_emojis import Slacker


def args():
    """ Defines the paramters of the program """
    parser = argparse.ArgumentParser(
        prog='slack_emojis',
        description='''
Count occurences of each custom emoji in Slack
''',
        formatter_class=argparse.RawTextHelpFormatter
    )
    parser.add_argument('token', help='Slack API token')
    return parser.parse_args()


def main(args):
    """ Main """
    Stats(Slacker(args.token)).save()


try:
    main(args())
except KeyboardInterrupt:
    sys.exit('\nERROR: Interrupted by user')
