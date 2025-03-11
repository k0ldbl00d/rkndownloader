<?php

function logw($message) {
    $d = date('d.m.Y H:i:s');
    echo "{$d}: {$message}\n";
}

function signExpired() {
    $xml = file_get_contents("/app/data/request.xml");
    if(!$xml) return "Невозможно прочесть файл запроса (request.xml)";
    if(preg_match("/\<requestTime\>([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})\.([0-9]{1,4})((\+|\-)[0-9]{2}:[0-9]{2})\<\/requestTime\>/i", $xml, $m)) {
        $time = strtotime($m[1].' '.$m[2]) + (365*86400);
        logw("Срок окончания действия цифровой подписи: " . date("d.m.Y, H:i",$time));
        $tdiff = $time - time();
        if($tdiff < 864000) {
            $diffdays = floor($tdiff/86400);
            if($diffdays>0)
                return "ВНИМАНИЕ!!! До истечения цифровой подписи осталось менее {$diffdays} суток!";
            if($diffdays==0)
                return "ВНИМАНИЕ!!! Цифровая подпись истекает сегодня!";
            if($diffdays<0)
                $diffdays = abs($diffdays);
                return "ВНИМАНИЕ!!! Цифровая подпись истекла {$diffdays} дней назад!";
        }
        return false;
    }
    return "Внимание!!! Ошибка при проверке файла запроса. Возможные причины: некорректный файл запроса.";
}
