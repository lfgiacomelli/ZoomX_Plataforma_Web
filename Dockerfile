FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    msmtp \
    msmtp-mta \
    ca-certificates \
    && docker-php-ext-install pdo_pgsql

COPY . /var/www/html/
COPY msmtprc.template /etc/msmtprc.template
COPY entrypoint.sh /entrypoint.sh

RUN a2enmod rewrite
RUN chmod +x /entrypoint.sh

# Configura PHP para usar msmtp
RUN echo "sendmail_path = /usr/bin/msmtp -t" >> /usr/local/etc/php/conf.d/mail.ini

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
