#!/bin/bash

# /*      _\|/_
#         (o o)
# +----oOO-{_}-OOo----------------------------------------------------------------------------------------------------+
# | Tuantq - https://github.com/tqt97                                                                                 |
# +------------------------------------------------------------------------------------------------------------------*/


#                                                  __   _,--="=--,_   __
#                                                 /  \."    .-.    "./  \
#                                                /  ,/  _   : :   _  \/` \
#                                                \  `| /o\  :_:  /o\ |\__/
#                                                 `-'| :="~` _ `~"=: |
#                                                    \`     (_)     `/ jgs
#                                             .-"-.   \      |      /   .-"-.
# .------------------------------------------{     }--|  /,.-'-.,\  |--{     }-----------------------------------------.
#  )                                         (_)_)_)  \_/`~-===-~`\_/  (_(_(_)                                        (
# (   This script will install the required dependencies and run the application                                                                                                           )
#  )                                                                                                                  (
# '--------------------------------------------------------------------------------------------------------------------'


# Grant executable permission to the script
if [[ ! -x "$0" ]]; then
    chmod +x "$0"
fi

# Setup color for output text
# http://stackoverflow.com/questions/5947742/how-to-change-the-output-color-of-echo-in-linux
# Black        0;30     Dark Gray     1;30
# Red          0;31     Light Red     1;31
# Green        0;32     Light Green   1;32
# Brown/Orange 0;33     Yellow        1;33
# Blue         0;34     Light Blue    1;34
# Purple       0;35     Light Purple  1;35
# Cyan         0;36     Light Cyan    1;36
# Light Gray   0;37     White         1;37

COLOR='\033[1;36m'
LIGHT_PURPLE='\033[1;35m'
LIGHT_BLUE='\033[1;34m'
LIGHT_GREEN='\033[1;32m'
NC='\033[0m' # No Color

printf "\n${LIGHT_PURPLE}💥 This script will install the required dependencies and run the application.\n"
echo -e

# Change directory to your Laravel project directory
# cd /path/to/your/laravel/project

# Check if composer.json exists
if [ -f composer.json ]; then
    # Run composer install
    composer install

    # echo -e "Composer installed successfully!"
    echo -e
    printf "${COLOR}💥  Composer installed successfully 👏🎉🎊\n"
    echo -e
else
    echo "composer.json not found. Are you in the correct directory?"
fi

# Check if package.json exists
if
    [ -f package.json ]; then
    # Run npm install
    npm install
    # echo -e "NPM installed successfully!"
    echo -e
    printf "${COLOR}💥 NPM installed successfully 👏🎉🎊\n"
    echo -e
else
    echo "package.json not found. Are you in the correct directory?"
fi

if [ ! -f .env ]; then
    echo -e ".env file not found. Copying .env.example to .env"
    cp .env.example .env
else
    echo -e
    echo -e "${LIGHT_GREEN}🙌 .env file already exists. Skipping copy"
    echo -e
fi

# Generate application key if not already set

if ! grep -q "APP_KEY=" .env ; then

    php artisan key:generate
else
    echo -e
    echo -e "${LIGHT_GREEN}🙌 APP_KEY already set. Skipping key generation"
    echo -e
fi

# run migrate and seed
php artisan migrate:fresh --seed

# run storage link
php artisan storage:link

# When successful installation, exit the script and print a success message
echo -e
printf "${LIGHT_PURPLE}💥 Setup Done! 👏🎉🎊\n"
echo -e

# Create admin filament user if it doesn't exist
printf "${LIGHT_BLUE}Create admin filament user"
echo -e

php artisan filament:user
