#!/usr/bin/env bash

ping -n 11 127.0.0.1 > nul
echo 'Creating Queue Exchange'
php bin/console rabbitmq:setup-fabric
php bin/console rabbitmq:consumer order_create_response &
php bin/console rabbitmq:consumer order_create_request &
php bin/console rabbitmq:consumer wine_update &

echo "Creating Database schema.."
php bin/console doctrine:schema:update --force
echo "Getting Wine Inventory..."
php bin/console app:load_rss_feed &
echo "Clearing cache"
php bin/console cache:clear --no-warmup --env=prod
php bin/console server:run

open http://localhost:8000