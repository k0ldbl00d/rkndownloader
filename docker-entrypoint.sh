echo Start RKN downloader...
while true
do
    php /app/download.php
    echo Waiting 30 minutes before next attempt
    sleep 30m
done
