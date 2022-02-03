FROM php:7.4-cli-alpine
WORKDIR /app
RUN mkdir -p /app/data
ADD src/* /app
COPY src/data/request* /app/data
COPY docker-entrypoint.sh /app
RUN chmod a+x /app/docker-entrypoint.sh
RUN apk add libxml2-dev && docker-php-ext-install soap
#ENTRYPOINT [ "/app/docker-entrypoint.sh" ]
