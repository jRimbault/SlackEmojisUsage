#!/usr/bin/env bash
#/ Script Name: bootstrap.sh
#/ Author: jRimbault
#/
#/ Description:
#/   Setup dev env inside a vagrant VM
#/   Should not be used outside the initial setup of
#/   the vagrant VM
#/
#/ Options:
#/   --help, -h display this help
usage()
{
  grep "^#/" "$0" | cut -c4-
  exit 0
}

set -euo pipefail
IFS=$'\n\t'

# setup minimal packages for dev env
packages_installation()
{
  local packages
  mapfile -t packages < <(cat /var/www/build/packages)
  apt-get update -q
  apt-get install -qy "${packages[@]}"
  apt-get autoremove -qy
}

# composer, npm, etc
# mv files around if necessary
dependencies_setup()
{
  composer install -d=/var/www
}

# setup the virtualhost
# and restart apache2 server
apache_setup()
{
  cp /var/www/build/virtualhost.conf /etc/apache2/sites-available/000-default.conf
  a2enmod rewrite
}

main()
{
  packages_installation
  dependencies_setup
  apache_setup
}

# executes only when executed directly not sourced
if [[ "${BASH_SOURCE[0]}" = "$0" ]]; then
  [[ "$*" =~ .*--help ]] > /dev/null ||
  [[ "$*" =~ .*-h ]] > /dev/null && usage
  main "$@"
fi
