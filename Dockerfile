FROM php:7.0-apache
LABEL version="1"
LABEL release="1"
LABEL name="Red Hat OpenShift - PHP & Database Demo"

# Install dependencies
RUN docker-php-ext-install mysqli

ENV HOME=/var/www/html
WORKDIR ${HOME}
COPY . .

# Change to run on 8080
ENV PORT=8080
EXPOSE 8080
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground

# Configure to run as a non-root user
RUN useradd -u 1337 -r -g 0 -d ${HOME} -s /sbin/nologin \
    -c "Default php-web user" phpweb

RUN chown -R 1337:0 ${HOME} && \
    find ${HOME} -type d -exec chmod g+ws {} \; && \
    chgrp -R 0 /etc/apache2 && \
    find /etc/apache2 -type d -exec chmod g+ws {} \; && \
    chgrp -R 0 /var/run/apache2 && \
    find /var/run/apache2 -type d -exec chmod g+ws {} \; && \
    chgrp -R 0 /var/lock/apache2 && \
    find /var/lock/apache2 -type d -exec chmod g+ws {} \;

USER 1337
