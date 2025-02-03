<?php

function logw($message) {
    $d = date('d.m.Y H:i:s');
    echo "{$d}: {$message}\n";
}

function signExpired() {
    $res = trim(shell_exec("openssl cms -noverify -cmsout -noout -print -inform DER -in /app/data/request.xml.p7s | grep notAfter"));
    if(preg_match('/notAfter: (.+)/',$res,$m)) {
        $time = strtotime($m[1]);
        logw("Срок окончания действия цифровой подписи: " . date("d.m.Y, H:i",$time));
        if($time - time() < 864000) {
            return "Внимание!!! До истечения цифровой подписи осталось менее 10 суток!";
        }
        return false;
    }
    return "Внимание!!! Ошибка при проверке файла цифровой подписи. Возможные причины: некорректный файл подписи.";
}
