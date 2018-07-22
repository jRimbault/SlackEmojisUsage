from django.core.management.base import BaseCommand, CommandError
from slack_emojis import secrets
from slack_emojis import Stats
from slack_emojis import Slacker

class Command(BaseCommand):
    help = 'Collect all the emojis and their current count'

    def handle(self, *args, **options):
        Stats(Slacker(secrets.TOKEN)).save()
