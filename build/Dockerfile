# should only run when called from the 'build/docker.sh' script
# the script will set the correct mount point and port forwarding
#
# Instructions:
#   docker build -t api.jrimbault.io ..
#   ./build/docker.sh -h

FROM chialab/php:7.2-apache

COPY build/virtualhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
EXPOSE 80
