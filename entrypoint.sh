#!/bin/bash
# Exit immediately if a command exits with a non-zero status
set -e

# 1. Run migrations
# This ensures your database schema is always up to date with your code.
echo "--- Running Migrations ---"
php artisan migrate --force

# 2. SMART SEEDING
# We check if users exist. If not, we seed the defaults.
echo "--- Checking if Seeding is needed ---"
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();")

if [ "$USER_COUNT" -eq "0" ]; then
    echo "--- Database empty. Running Seeders... ---"
    php artisan db:seed --force
else
    echo "--- Data already exists ($USER_COUNT users). Skipping Seeder. ---"
fi

# 3. GUARANTEE: The "Fail-Safe" Admin
# This ensures you can ALWAYS log in, even if your seeders are messy.
echo "--- Ensuring Admin User exists ---"
php artisan tinker --execute="\App\Models\User::updateOrCreate(['username' => 'admin'], ['name' => 'System Admin', 'password' => \Hash::make('admin123')])"

# 4. Optimization for Production
# Instead of clearing, we "cache" the config for faster performance on Render.
echo "--- Optimizing Laravel for Production ---"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Hand over control to Apache
# This is crucial: it replaces the shell script process with the web server process.
echo "--- Starting Apache ---"
exec apache2-foreground