#!/bin/sh

while [ true ]
    do
      php /var/www/artisan schedule:run &
      sleep 60
    done
