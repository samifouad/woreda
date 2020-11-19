FROM php:7.4-apache
RUN a2enmod rewrite
RUN service apache2 restart

COPY . /var/www/html/

RUN chmod 755 -R /var/www/html
RUN chown -R www-data:www-data /var/www/html
