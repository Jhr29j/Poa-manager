# Dockerfile para aplicación PHP con MySQL
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar archivos de la aplicación
COPY . /var/www/html/

# Configurar puerto y permisos
EXPOSE 8080
RUN chown -R www-data:www-data /var/www/html