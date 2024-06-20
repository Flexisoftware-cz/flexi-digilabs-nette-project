# Flexi testing project

## System requirements

PHP >= 8.1, PHP GD extension

## Inslallation

- Clone GIT repository from https://github.com/Flexisoftware-cz/flexi-digilabs-nette-project.git
- Create directories for temp and logs and set writable permissions for these directories for web server user, e.g. www-data and OS Linux
```
  mkdir log temp
  chown www-data:www-data log temp
```
- Install application by composer, composer file is included in GIT repository
```
  php composer.phar install
```
- Set the document root of the virtualhost of the web server to the directory www

Debug mode is permanently enabled. Can be turned off or set in Bootstrap.php

## Application features

### Action 1

Shows MEME image with automatic calculation font size.

### Action 2

Shows names with the same letter at the beginning of the first name and last name.

### Action 3

Shows math calculations for division with an even divisor

### Action 4

Shows date and times in interval -1 month and +1 month

### Action 5

Shows correct math calculations. It handles the arithmetic operations of addition, subtraction, multiplication, and division on both sides of an equation. Can't work with parentheses and prioritizing operations.




