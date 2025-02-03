FROM php:7.4-cli-alpine
WORKDIR /app
RUN apk add libxml2-dev libzip-dev rrdtool font-misc-misc font-screen-cyrillic font-alias freetype ttf-dejavu pango fontconfig font-sony-misc && docker-php-ext-install soap zip curl
RUN mkdir -p /app/data
ADD src/*.php /app/
COPY docker-entrypoint.sh /app
ENTRYPOINT [ "sh", "docker-entrypoint.sh" ]
