docker build --tag rabbitmq -< rabbitmq/Dockerfile
docker run -d --hostname winery --name winery -p 5674:5672 -p 8086:15672 rabbitmq:3.7.4
ping -n 11 127.0.0.1 > nul
php bin/console rabbitmq:setup-fabric
php bin/console rabbitmq:consumer order_create_response &
php bin/console rabbitmq:consumer order_create_request &
php bin/console rabbitmq:consumer wine_update &
exit