# Utilise l'image PHP 8.2 avec Apache
FROM php:8.2-apache

# Définit le répertoire de travail dans l'image
WORKDIR /var/www/html

# Copie les fichiers locaux dans le répertoire de travail de l'image
COPY . .

# Ajoute "ServerName localhost" à la configuration d'Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Met à jour le système et installe divers paquets nécessaires
RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
     vim \
     locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

# Configure les paramètres régionaux de l'environnement
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Télécharge et installe Composer, puis le déplace dans un répertoire accessible
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Configure l'extension intl pour PHP
RUN docker-php-ext-configure intl

# Installe diverses extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite

# Installe et active l'extension APCu pour la mise en cache en PHP
RUN pecl install apcu && docker-php-ext-enable apcu
    
# Ajoute le script d'installation des extensions PHP depuis un emplacement distant
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Rend le script exécutable, synchronise le système de fichiers et installe l'extension amqp
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp
