FROM php:8.2-apache

# 1. Cài extension mysqli như cũ
RUN docker-php-ext-install mysqli

# 2. BẬT MODULE REWRITE (Cực kỳ quan trọng cho file .htaccess của ông)
RUN a2enmod rewrite

# 3. Cho phép Apache đọc file .htaccess trong thư mục /var/www/html
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 4. Copy mã nguồn
COPY . /var/www/html/

# 5. Cấp quyền cho Apache (để tránh lỗi permission)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80