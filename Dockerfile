# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html/

# Instala extensiones necesarias de PHP, incluyendo MySQLi
RUN docker-php-ext-install mysqli

# Da permisos a los archivos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Habilita módulos de Apache si es necesario
RUN a2enmod rewrite

# Expone el puerto 80
EXPOSE 80
