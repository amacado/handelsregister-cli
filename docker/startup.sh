#!/bin/bash

cd /app || exit

# Install dependencies
echo "Install composer dependencies.."
composer install

# Install dusk driver
echo "Install chrome dusk driver.."
php handelsregister-cli dusk:chrome-driver

# Keep the container running
echo "================================================================"
echo "Development environment is prepared. Ready to accept connection."
echo "================================================================"

tail -f /dev/null
