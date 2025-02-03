<?php

function tgsend($text) {
    try {
        if(!is_string($text) || empty($text) || strlen(trim($text))==0) return false;

        $token = getenv("TELEGRAM_BOT_TOKEN");
        if(empty($token)) return false;
        
        $chat_id = getenv("TELEGRAM_BOT_CHAT_ID");
        if(empty($chat_id)) return false;

        $url = "https://api.telegram.org/bot{$token}";
        $request_body = [
            'chat_id' => $chat_id, 
            'text' => trim($text)
        ];

        $ch = curl_init($url . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($request_body));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}