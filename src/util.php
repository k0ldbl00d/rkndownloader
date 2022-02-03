<?php

function logw($message) {
    $d = date('d.m.Y H:i:s');
    echo "{$d}: {$message}\n";
}
