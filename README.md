# RKNDownloader

1. Put request.xml and request.xml.p7s into src/data folder
2. Build
3. Run

```
docker build -t rknloader .
```

Run in foreground
```
docker run -it --rm rknloader
```

Run as daemon
```
docker run -d --restart=always -v data:/app/data rknloader
```

