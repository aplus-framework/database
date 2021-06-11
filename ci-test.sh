#/bin/bash
set -e
mkdir -p build
echo "------------------------"
echo "Running Composer Install"
echo "------------------------"
echo
composer install
echo
echo "--------------------------"
echo "Running Composer Normalize"
echo "--------------------------"
echo
composer normalize --dry-run --indent-size=1 --indent-style=tab
echo
echo "--------------------"
echo "Running PHP-CS-Fixer"
echo "--------------------"
echo
vendor/bin/php-cs-fixer fix --diff --dry-run --verbose
echo
echo "-----------------------"
echo "Running PHPStan Analyse"
echo "-----------------------"
echo
vendor/bin/phpstan analyse -vvv
echo
echo "---------------"
echo "Running PHPUnit"
echo "---------------"
echo
vendor/bin/phpunit
echo
echo "---------------------"
echo "Running phpDocumentor"
echo "---------------------"
echo
phpdoc
