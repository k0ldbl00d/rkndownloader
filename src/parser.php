<?php

class Parser {
    function readZip($zipFileName) {

        $xml = file_get_contents("zip://{$zipFileName}#register.xml");
        if(!$xml) {
            logw("Ошибка при чтении ZIP-архива");
            return false;
        }

        if(preg_match_all("/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(\/[0-9]{1,3})?)/", $xml, $matches)) {
            $ips = array_unique($matches[1]);
            file_put_contents( "./data/social-ips.txt", implode( "\n", $ips ) );
            $this->mkMikrotikScript($ips);
            logw("Найдено IP-адресов: " . count($ips));
        } else {
            logw("В файле не найдены IP-адреса");
        }

    }

    function mkMikrotikScript($ips, $listname="social-sites") {

        $lines = array_map(function($ip) use ($listname) {
            return "add list={$listname} address={$ip} comment=\"Socially significant resources\"";
        }, $ips);
        $script = "/ip firewall address-list\n\nremove [ find where list={$listname} ]\n\n" . implode("\n",$lines) . "\n";
        file_put_contents("./data/mikrotik-social.rsc", $script);
    }
}