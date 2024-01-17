composer install
systemctl restart php-fpm
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
php artisan optimize:clear
php artisan config:cache