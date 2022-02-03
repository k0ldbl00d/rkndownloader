FROM php:7.4-cli-alpine
WORKDIR /app
RUN mkdir -p /app/data
ADD src/*.php /app
COPY docker-entrypoint.sh /app
RUN apk add libxml2-dev && docker-php-ext-install soap
ENTRYPOINT [ "sh", "docker-entrypoint.sh" ]
