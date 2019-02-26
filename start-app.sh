#!/usr/bin/env bash

echo "Creating Database schema.."
php bin/console doctrine:schema:update --force
php bin/console server:run

echo "Getting Wine Inventory..."
php bin/console app:load_rss_feed &
echo "Clearing cache"
php bin/console cache:clear --no-warmup --env=prod

open http://localhost:8000