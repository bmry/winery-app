#!/usr/bin/env bash

php bin/console rabbitmq:setup-fabric
php bin/console rabbitmq:consumer order_create_response
php bin/console rabbitmq:consumer order_create_request
bin/console rabbitmq:consumer wine_update
php bin/console server:run
php bin/console doctrine:schema:update --force
php bin/console app:load_rss_feed
open "http://localhost:8000