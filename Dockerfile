FROM php:8.2-apache

# Cài đặt extension kết nối MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Bật mod_rewrite cho file .htaccess nếu có
RUN a2enmod rewrite

# Copy toàn bộ mã nguồn vào thư mục web server
COPY . /var/www/html/

EXPOSE 80