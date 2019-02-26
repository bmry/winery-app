ping -n 11 127.0.0.1 > nul
php bin/console rabbitmq:setup-fabric
start cmd.exe @cmd /k "php bin/console rabbitmq:consumer order_create_response"
start cmd.exe @cmd /k "php bin/console rabbitmq:consumer order_create_request"
start cmd.exe @cmd /k "php bin/console rabbitmq:consumer wine_update"
start cmd.exe @cmd /k "php bin/console doctrine:schema:update --force"
start cmd.exe @cmd /k "php bin/console server:run"
start cmd.exe @cmd /k "php bin/console app:load_rss_feed"
php bin/console cache:clear --no-warmup --env=prod
start http://localhost:8000