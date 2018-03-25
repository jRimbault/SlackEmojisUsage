#!/usr/bin/env bash
#/ Author: jRimbault
#/
#/ Description:
#/   Wrapper around `docker run`
#/   Mostly because I want to mount the project root as a volume
#/   each time I spin up a container
#/
#/ Usage:
#/   ./build/docker.sh [container name] [mount point]
#/
#/ Options:
#/   <container name> defaults to project name (lowercased)
#/   <mount point>    defaults to '/var/www'
#/   --help, -h       display this help
usage()
{
  grep "^#/" "$0" | cut -c4-
  exit 0
}

# exit on fail
set -euo pipefail
# fix arrays, on newline and tab, not space
IFS=$'\n\t'

# get the absolute path to the script
readonly SCRIPTPATH=$(readlink -f -- "$0")
# assumes the script is one directory inside the project root
readonly WORKDIR=$(dirname "${SCRIPTPATH%/*}")

# get the project name lowercased
# if it was doable in one instruction, it would be a global readonly
project()
{
  local name
  name=$(basename "$WORKDIR")
  echo "${name,,}"
}

main()
{
  local nametag
  local mountpoint
  nametag=${1:-"$(project)"}
  mountpoint=${2:-"/var/www"}
  docker run -d -p 80:80 --name "$nametag-$RANDOM" -v "$WORKDIR:$mountpoint" "$nametag"
}

# executes only when executed directly, not sourced
if [[ "${BASH_SOURCE[0]}" = "$0" ]]; then
  [[ "$*" =~ .*--help ]] > /dev/null ||
  [[ "$*" =~ .*-h ]] > /dev/null && usage
  main "$@"
fi
