<?php

require_once("util.php");
require_once("model.php");
require_once("downloader.php");
require_once("parser.php");
require_once("telegram.php");

ini_set('default_socket_timeout', 600);
ini_set('memory_limit', '512M');
ini_set('user_agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');

if($err = signExpired()) {
    logw($err);
    tgsend($err);
}

$dl = new Downloader();

if($dl->isNewDumpAvail()) {
    $dl->download();
} else {
    logw("Файл выгрузки актуален, нечего тут делать");
}
logw("Работа скрипта завершена");