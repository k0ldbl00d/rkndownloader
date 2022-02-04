<?php

require_once("util.php");
require_once("model.php");
require_once("downloader.php");
require_once("parser.php");

$dl = new Downloader();

if($dl->isNewDumpAvail()) {
    $dl->download();
} else {
    logw("Файл выгрузки актуален, нечего тут делать");
}
logw("Работа скрипта завершена");