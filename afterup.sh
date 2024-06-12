# Node.js バージョン変更
# laravel cache clear
# 2024/05/18

n 17.9.1
hash -r
npm install
npm run build

php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear