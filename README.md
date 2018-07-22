# Slats


Requirements:
- Python 3.6
- pip/pipenv


### Usage:

First you'll have to get a slack token for your team.
Write it in a file named `slack_emojis/secrets.py` :

```python
TOKEN = "your-token-07655"
```

To install and launch the app :

```shell
pip install --user pipenv
pipenv shell
pipenv install
# create database
python manage.py migrate
# first collect of emojis
python manage.py collect_emojis
# launch django
python manage.py runserver
```

Eventually you'll want to run `collect_emojis` regularly.
Using crontab :

```shell
# to get the project's python interpreter path
pipenv --venv
crontab -e
```

Write the path you just got here:
```text
0 * * * * /path/to/env/python /path/to/manage.py collect_emojis
```
