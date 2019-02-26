#!/usr/bin/env bash

php bin/console server:run
php bin/console doctrine:schema:update --force
php bin/console app:load_rss_feed
open "http://localhost:8000