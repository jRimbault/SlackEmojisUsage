

class Stats(object):
    def __init__(self, slack):
        """ Make statistics """
        self.emojis = slack.count()
        self.urls = slack.emoji.list().body['emoji']

    def save(self):
        from api.model import Emoji
        from api.model import Count
        for name, count in self.emojis.items():
            print(name, count, self.urls[name])
            url = self.urls[name]
            if 'alias' in url:
                url = ''
            emoji, created = Emoji.objects.get_or_create(name=name, url=url)
            Count.objects.create(count=count, fk_emoji_id=emoji.id)
