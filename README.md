# RKNDownloader

1. Put request.xml and request.xml.p7s into "data" folder
2. Build
3. Run

== Build
```
docker build -t rknloader .
```

== Run in foreground
```
docker run -it --rm -v data:/app/data rknloader
```

== Run as daemon
```
docker run -d --restart=always -v data:/app/data rknloader
```

