echo Start RKN downloader...
while true
do
    php /app/download.php
    echo Waiting 30 minutes before next attempt
    if [ ! -f /app/data/info.rrd ]; then
        echo "No RRD file, creating it..."
        rrdtool create /app/data/info.rrd --step 1800 \
        DS:ips:GAUGE:3600:0:4294967295 \
        DS:records:GAUGE:3600:0:4294967295 \
        DS:ips_soc:GAUGE:3600:0:4294967295 \
        DS:records_soc:GAUGE:3600:0:4294967295 \
        RRA:AVERAGE:0.5:1:17520 \
        RRA:MIN:0.5:48:17520 \
        RRA:MAX:0.5:48:17520 \
        RRA:AVERAGE:0.5:48:17520
    fi
    SOC_IPS=`cat /app/data/social-ips.txt | wc -l`

    echo "Updating RRD archive..."
    rrdtool update /app/data/info.rrd N:0:0:$SOC_IPS:0

    rrdtool graph /app/data/stat.png --imgformat PNG \
    DEF:socips=/app/data/info.rrd:ips_soc:AVERAGE \
    LINE1:socips#0000FF:"Social IPs\l"

    sleep 30m
done
