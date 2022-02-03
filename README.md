# RKN Downloader

Приложение для выгрузки реестров РосКомНадзора операторами связи.

Поддерживаются:

* Единый реестр доменных имён, указателей страниц сайтов в сети «Интернет» и сетевых адресов, позволяющих идентифицировать сайты в сети «Интернет», содержащие информацию, распространение которой в Российской Федерации запрещено - Приказ Роскомнадзора от 14.12.2017 № 249, Приказ Роскомнадзора от 21.02.2013 № 170
* Перечень отечественных социально значимых информационных ресурсов в информационно-телекоммуникационной сети «Интернет» — Приказ Минкомсвязи России от 31.03.2020г. № 148

## How-to

Потребуется любой Linux (Centos, Ubuntu, etc) с установленным docker или Windows с Docker Desktop. Для Windows путь к каталогу с данными указывается через «\».

1. Создать каталог для данных, например ```/opt/rknd/data```, куда положить файлы ```request.xml``` и ```request.xml.p7s``` - запрос на выгрузку и цифровая подпись к нему. Цифровую подпись можно сделать в Крипто-Про или Крипто-АРМ.
2. Собрать docker-образ (команда ниже)
3. Запустить docker-контейнер в режиме отладки, проверить что реестры выгружаются, остановить и запустить уже в режиме демона для постоянной работы

Приложение проверяет доступность нового файла реестра раз в 30 минут. Если доступна новая версия реестра - она скачивается и помещается в каталог ```data```.

В результате появятся два файла:

* ```latest.zip``` - Запрещённые сайты
* ```latest-soc.zip``` - Социально значимые сайты

Возможен запуск нескольких контейнеров, если у вас несколько организаций-операторов, просто запустите несколько экземпляров контейнера, указав разные каталоги ```data``` в опции -v

## Сборка образа
Скачиваем исходники в виде ZIP-архива или git'ом:
```
git clone https://github.com/k0ldbl00d/rkndownloader.git
```
Собираем образ:
```
docker build -t rknloader .
```

## Примеры запуска
Запуск в интерактивном режиме:
```
docker run -it --rm -v /opt/rknd/data:/app/data rknloader
```

Запуск в режиме демона:
```
docker run -d --restart=always -v /opt/rknd/data:/app/data rknloader
```

Запуск в режиме демона в Windows:
```
docker run -d --restart=always -v D:\rknd\data:/app/data rknloader
```

## Обновление
1. Подтянуть свежую версию (git pull в каталоге с исходниками)
2. Остановить и удалить старый контейнер
3. Повторить сборку образа
4. Запустить

## Функции, которые планируется реализовать
1. Формирование plain-text списка IP-адресов и подсетей для реестра социально значимых ресурсов
2. Формирование скрипта для Mikrotik RouterOS
3. Уведомления в Telegram об ошибках обновления
4. Уведомление об истечении срока действия цифровой подписи
