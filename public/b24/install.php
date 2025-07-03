<?php
// Получаем параметры установки от Bitrix24
$auth   = $_REQUEST['auth']   ?? '';
$domain = $_REQUEST['domain'] ?? '';

if (!$auth || !$domain) {
    header('HTTP/1.1 400 Bad Request');
    exit('Missing auth or domain');
}

// Папка для хранения ключей по доменам
$keyDir = __DIR__ . '/keys';
if (!is_dir($keyDir)) mkdir($keyDir, 0755, true);

// Файл с ключом для данного домена
$keyFile = "$keyDir/{$domain}.key";

// Генерируем ключ при первом вызове install
if (!file_exists($keyFile)) {
    $apiKey = 123456;
    file_put_contents($keyFile, $apiKey);
} else {
    $apiKey = file_get_contents($keyFile);
}

// При желании сохраняем $auth для дальнейших REST-запросов
 file_put_contents("$keyDir/{$domain}.auth", $auth);

// Возвращаем Bitrix24 подтверждение установки
header('Content-Type: text/plain; charset=UTF-8');
echo "INSTALL_OK\nВаш API-ключ: {$apiKey}";
