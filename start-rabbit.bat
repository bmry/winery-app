@ECHO OFF

docker build --tag rabbitmq -< rabbitmq/Dockerfile
docker run -d --hostname winery --name winery -p 5674:5672 -p 8086:15672 rabbitmq:3.7.4
