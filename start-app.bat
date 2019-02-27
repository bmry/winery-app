@ECHO OFF

ping -n 11 127.0.0.1 > nul
echo Setting Up Queue
php bin/console rabbitmq:setup-fabric
echo Setting Up  Consumers
start /b php bin/console rabbitmq:consumer order_create_response
ping -n 11 127.0.0.1 > nul
start /b php bin/console rabbitmq:consumer order_create_request
ping -n 11 127.0.0.1 > nul
start /b php bin/console rabbitmq:consumer wine_update

echo Creating Database
php bin/console doctrine:schema:update --force

echo Loading wines into database
php bin/console app:load_rss_feed

echo Clearing cache
php bin/console cache:clear --no-warmup --env=prod

echo Starting application
php bin/console server:run

start http://localhost:8000