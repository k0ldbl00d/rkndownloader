FROM debian:bookworm-slim
WORKDIR /app
RUN apt update && apt install -y php8.2-cli php8.2-curl php8.2-zip php8.2-soap rrdtool libfreetype6 fonts-dejavu fonts-roboto fontconfig openssl
RUN mkdir -p /app/data
ADD src/*.php /app/
COPY docker-entrypoint.sh /app
ENTRYPOINT [ "sh", "docker-entrypoint.sh" ]
