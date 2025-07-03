<?php
// public/b24/handler.php

// 1) При установке от Bitrix24 придёт auth, domain и т.п.
$auth   = $_REQUEST['auth']   ?? '';
$domain = $_REQUEST['domain'] ?? '';

if ($_SERVER['REQUEST_METHOD']==='GET' && $auth && $domain) {
    // Генерируем или достаём существующий ключ:
    $keyFile = __DIR__ . "/keys/{$domain}.key";
    $bytes = file_put_contents($keyFile, bin2hex(random_bytes(16)));
    if ($bytes === false) {
        var_dump("Не удалось записать ключ в $keyFile");
    } else {
        var_dump("Ключ записан, байт: $bytes");
    }
    if (!file_exists($keyFile)) {
        file_put_contents($keyFile, bin2hex(random_bytes(16)));
    }
    // Можно сохранить $auth для REST подмероприятий
    // ...

    // Обязательно вернуть INSTALL_OK
    header('Content-Type: text/plain; charset=UTF-8');
    echo "INSTALL_OK";
    exit;
}

// 2) Обработка вебхуков (например, OnTaskUpdate)
if ($_SERVER['REQUEST_METHOD']==='POST') {
    // Читать JSON или form-data, фильтровать только свои события
    http_response_code(200);
    echo json_encode(['result'=>'ok']);
    exit;
}

// Для прочих запросов можно возвращать 404
http_response_code(404);
