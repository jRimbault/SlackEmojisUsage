#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import json
import datetime
import subprocess
from pathlib import Path


def grep(needle, path):
    return len(subprocess.run(
        ['grep', '-ro', needle, str(Path(path).resolve())],
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        universal_newlines=True,
    ).stdout.splitlines())


def ripgrep(neelde, path):
    return subprocess.run(
        ['rg', '--no-filename', '-c', neelde, str(Path(path).resolve())],
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        universal_newlines=True,
    ).stdout.splitlines()


def print_date(timestamp):
    return datetime.datetime.fromtimestamp(
        float(timestamp)
    ).strftime('%Y-%m-%d %H:%M:%S')


def mkdir(path):
    path = Path(path)
    if not path.exists():
        path.mkdir()


def write_file_json(path, data):
    Path(path).open('w').write(json.dumps(data))
