from django.http import HttpResponse
from django.http import JsonResponse
from django.shortcuts import render
from api import data
from api.sentences import SENTENCES
import random


def index(request):
    return render(
        request,
        'index.html',
        context={
            'emojis': data.Emoji.snapshot()
        },
        content_type='text/html'
    )


def graph(request, n=5):
    return JsonResponse(data.Emoji.graph(n), safe=False)


def text(request):
    text = [random.choice(SENTENCES)]
    for emoji in data.Emoji.snapshot()[:10]:
        text.append(":%s: : %s" % (emoji['name'], emoji['count']))
    return HttpResponse('\n'.join(text), content_type='text/plain')


def javascript(*args, **kwargs):
    return render(*args, **kwargs, content_type='application/javascript')


def chartoptions_js(r):
    return javascript(r, 'js/chartoptions.js')


def colors_js(r):
    return javascript(r, 'js/colors.js')


def chart_js(r):
    return javascript(r, 'js/chart.js')


def main_js(r):
    return javascript(r, 'js/main.js')
