import datetime
from django.db import models
from api import data


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
        for count in data.Count.last(self.id, 1680):
            points[0].append(count.count)
            points[1].append(str(count.date))
        return points

    def count(self):
        return data.Count.last(self.id, 1)[0].count

    def to_dict(self):
        return {
            'name': self.name,
            'url': self.url,
            'count': self.count(),
        }

    def __str__(self):
        return str(self.to_dict())


class Count(models.Model):
    fk_emoji = models.ForeignKey(Emoji, on_delete=models.CASCADE)
    count = models.IntegerField()
    date = models.DateTimeField(default=datetime.datetime.utcnow().replace(tzinfo=datetime.timezone.utc).isoformat())

    class Meta:
        db_table = 'count'
