composer install
php artisan optimize:clear
systemctl restart php-fpm
php artisan config:cache