from django.http import HttpResponse
from django.shortcuts import render
from api import model
from api.sentences import SENTENCES
import json
import random


def index(request):
    return render(
        request,
        'index.html',
        context={
            'emojis': model.Emoji.snapshot()
        },
        content_type='text/html'
    )


def graph(request, n=5):
    return HttpResponse(
        json.dumps(model.Emoji.graph(n)),
        content_type='application/json'
    )


def text(request):
    text = []
    for emoji in model.Emoji.snapshot()[:10]:
        text.append(":%s: : %s" % (emoji['name'], emoji['count']))
    return HttpResponse(
        random.choice(SENTENCES)
        + '\n'
        + '\n'.join(text),
        content_type='text/plain'
    )


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
