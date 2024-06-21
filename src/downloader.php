<?php

class Downloader {

    private $wsdl = "http://vigruzki.rkn.gov.ru/services/OperatorRequest/?wsdl";
    private $loc = "http://vigruzki.rkn.gov.ru/services/OperatorRequest/";

    private $soap;

    private $responseCode = null;

    private $delay = 180;

    function __construct()
    {
        // Create context
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $this->soap = new SoapClient($this->wsdl, [
            'location' => $this->loc,
            'exceptions' => 1,
            'trace' => 1,
            'soap_version' => SOAP_1_1,
            'stream_context' => $context
        ]);
    }

    function getLocalDumpDate () {
        if(!file_exists("./data/lastdumpdate")) return null;
        return file_get_contents("./data/lastdumpdate");
    }

    function setLocalDumpDate ($date) {
        return file_put_contents("./data/lastdumpdate", $date);
    }

    function getRemoteDumpDate () {
        try {
            $response = $this->soap->getLastDumpDate();
            return $response->lastDumpDate/1000;
        } catch (SoapFault $e) {
            logw('Ошибка протокола SOAP при вызове метода getRemoteDumpDate');
            die();
        }
        return null;
    }

    function isNewDumpAvail() {
        if( $this->getLocalDumpDate() == null ){
            logw("Локальный файл реестра отсутствует, необходима загрузка");
            return true;
        }
        $remote = $this->getRemoteDumpDate();
        logw("Дата последнего реестра на сайте РКН: " . date("d.m.Y, H:i:s", $remote));
        logw("Дата локального реестра: " . date("d.m.Y, H:i:s", $this->getLocalDumpDate()));
        if( $this->getLocalDumpDate() < $remote ) return $remote;
        return false;
    }

    function download() {
        // Send request to download file
        $req = new sendRequest;

        $req->requestFile = file_get_contents('./data/request.xml');
        $req->signatureFile = file_get_contents('./data/request.xml.p7s');
        $req->dumpFormatVersion = "2.4";

        try {
            $response = $this->soap->sendRequest($req);
            if(!empty($response->result) && $response->result == 1) {
                $this->responseCode = $response->code;
                logw("Запрос на выгрузку отправлен. Код запроса: {$response->code}");
               } else {
                logw("Сбой при отправке запроса на выгрузку: {$response->resultComment}");
                return false;
            }

            $tries = 5;
            $gr = new getResult();
            $gr->code = $this->responseCode;

            logw("Ждём {$this->delay} секунд");
            sleep($this->delay);

            logw("Скачиваем файл реестра запрещенных ресурсов");
            while($tries>0) {
                $getResultResponse = $this->soap->getResult($gr);
                if($getResultResponse->result == 1) {
                    $tries = -1;
                    $file = $getResultResponse->registerZipArchive;
                    file_put_contents("./data/latest.zip", $file);
                    logw("Файл реестра запрещенных ресурсов успешно выгружен.");
                    $this->setLocalDumpDate( $this->getRemoteDumpDate() );
                } else {
                    $tries--;
                    logw("Осталось попыток: {$tries}; Ответ сервера: ".$getResultResponse->resultComment);
                    logw("Ждём {$this->delay} секунд");
                    sleep($this->delay);
                }
            }

            if($tries==0) {
                logw("Число попыток исчерпано, файл реестра запрещенных сайтов не получен.");
            }

            $tries = 5;
            logw("Скачиваем файл реестра социально-значимых ресурсов");
            while($tries>0) {
                $getResultResponse = $this->soap->getResultSocResources($gr);
                if($getResultResponse->result == 1) {
                    $tries = -1;
                    $file = $getResultResponse->registerZipArchive;
                    file_put_contents("./data/soc-latest.zip", $file);
                    logw("Файл реестра социально-значимых ресурсов успешно выгружен.");
                    $this->setLocalDumpDate( $this->getRemoteDumpDate() );

                    // Разбираем архив
                    $parser = new Parser();
                    $parser->readZip("./data/soc-latest.zip");

                } else {
                    $tries--;
                    logw("Осталось попыток: {$tries}; Ответ сервера: ".$getResultResponse->resultComment);
                    logw("Ждём ещё две минуты");
                    sleep(120);
                }
            }

            if($tries==0) {
                logw("Число попыток исчерпано, файл реестра социально-значимых ресурсов не получен.");
            }
        } catch (Exception $e) {
            logw("Произошла ошибка при выгрузке реестра: " . $e->getMessage());
            return false;
        }
        return false;
    }
}