from django.db import models


def is_num(n):
    try:
        int(n)
        return True
    except ValueError:
        return False


class Emoji(models.Model):
    name = models.CharField(max_length=255)
    url = models.CharField(max_length=255)

    class Meta:
        db_table = 'emoji'

    def to_graph_element(self):
        return {
            'name': self.name,
            'url': self.url,
            'data': self.plot_points()
        }

    def plot_points(self):
        points = [[], []]
        for count in Count.last(self.id, 1680):
            points[0].append(count.count)
            points[1].append(str(count.date))
        return points

    def count(self):
        return Count.last(self.id, 1)[0].count

    def to_dict(self):
        return {
            'name': self.name,
            'url': self.url,
            'count': self.count(),
        }

    def __str__(self):
        return str(self.to_dict())

    @staticmethod
    def graph(n=5):
        if is_num(n):
            return Emoji._multiple(int(n))
        return [Emoji._single(n)]

    @staticmethod
    def snapshot():
        return sorted(
            [e.to_dict() for e in Emoji.objects.all()],
            key=lambda e: e['count'],
            reverse=True
        )

    @staticmethod
    def _multiple(n):
        return sorted(
            [e.to_graph_element() for e in Emoji.objects.all()],
            key=lambda i: sum(i['data'][0]),
            reverse=True
        )[0:n]

    @staticmethod
    def _single(name):
        return Emoji.objects.filter(name=name)[0].to_graph_element()


class Count(models.Model):
    fk_emoji = models.ForeignKey(Emoji, on_delete=models.CASCADE)
    count = models.IntegerField()
    date = models.DateField()

    class Meta:
        db_table = 'count'

    @staticmethod
    def last(emoji_id, n=1680):
        return Count.objects.filter(fk_emoji=emoji_id).order_by('-date')[:n]
