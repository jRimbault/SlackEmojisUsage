from collections import Counter
from slacker import Slacker


class EmojiCounter(Slacker):
    """
    Extend Slacker to expose my own methods
    """

    def list(self):
        """ Parse Slack's API response to get only the list of Emojis """
        for emoji in self.emoji.list().body['emoji']:
            yield emoji

    def _channels(self):
        """ Get all the public channels from Slack """
        for channel in self.channels.list(exclude_archived=True).body['channels']:
            yield channel['id']

    def count(self):
        messages = []
        for c in self._channels():
            messages.extend(self.messages(c))
        return self.counter(messages)

    def counter(self, messages):
        count = Counter([word for m in messages for word in m.split(' ')])
        return {e: count[':' + e + ':'] for e in self.list()}

    def messages(self, channel):
        messages = []
        last_timestamp = None
        while True:
            r = self._history(channel, last_timestamp)
            messages.extend([m['text'] for m in r['messages']])
            if not r['has_more']:
                break
            last_timestamp = r['messages'][-1]['ts']

        return messages

    def _history(self, channel, last_timestamp):
        return self.channels.history(
            channel=channel,
            latest=last_timestamp,
            oldest=0,
            count=100
        ).body
