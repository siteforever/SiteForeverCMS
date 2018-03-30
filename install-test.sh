#!/bin/bash
sh -c "mysql -e 'create database IF NOT EXISTS siteforever_test;'"
curl -sS https://getcomposer.org/installer | php
php5 composer.phar install -o --no-interaction
