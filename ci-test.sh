#!/bin/bash
set -e

commands=(
    "composer install"
    "composer normalize --dry-run --indent-size=4 --indent-style=space"
    "vendor/bin/php-cs-fixer fix --diff --dry-run --verbose"
    "vendor/bin/phpmd src xml phpmd.xml"
    "vendor/bin/phpstan analyse -vvv"
    "vendor/bin/phpunit"
    "phpdoc"
)

color_default='\033[0m'
color_green='\033[1;32m'
color_red='\033[1;31m'

for command in "${commands[@]}"; do
    echo -e "${color_green}$ ${command}${color_default}"
    if ! eval "${command}"; then
        echo -e "${color_red}ERROR: Test failed${color_default}"
        exit
    fi
done

echo
echo -e "${color_green}Test succeeded${color_default}"
