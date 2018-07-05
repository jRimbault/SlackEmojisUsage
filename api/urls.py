"""api URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/2.0/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.urls import path
from api import views


def javascript(url, *args, **kwargs):
    return path('js/' + url, *args, **kwargs)


url_js = [
    javascript('chartoptions.js', views.chartoptions_js, name='chartoptions_js'),
    javascript('colors.js', views.colors_js, name='colors_js'),
    javascript('chart.js', views.chart_js, name='chart_js'),
    javascript('main.js', views.main_js, name='main_js'),
]

url_data = [
    path('slack/data/emoji/<n>', views.graph),
]

url_html = [
    path('', views.index),
]

urlpatterns = url_js + url_data + url_html
