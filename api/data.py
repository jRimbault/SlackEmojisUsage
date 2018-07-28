from api import model


def is_num(n):
    try:
        int(n)
        return True
    except ValueError:
        return False


class Emoji(object):
    @staticmethod
    def graph(n=5):
        if is_num(n):
            return Emoji._multiple(int(n))
        return [Emoji._single(n)]

    @staticmethod
    def snapshot():
        return sorted(
            [e.to_dict() for e in model.Emoji.objects.all()],
            key=lambda e: e['count'],
            reverse=True
        )

    @staticmethod
    def _multiple(n):
        return sorted(
            [e.to_graph_element() for e in model.Emoji.objects.all()],
            key=lambda i: sum(i['data'][0]),
            reverse=True
        )[0:n]

    @staticmethod
    def _single(name):
        return model.Emoji.objects.filter(name=name)[0].to_graph_element()


class Count(object):
    @staticmethod
    def last(emoji_id, n=1680):
        return model.Count.objects.filter(fk_emoji=emoji_id).order_by('-date')[:n]
