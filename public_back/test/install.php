<?php
// Путь к файлу лога (убедись, что папка writable)
$logFile = __DIR__ . '/install.log';

// Собираем параметры из GET и POST
$params = [
    'timestamp' => date('c'),
    'method'    => $_SERVER['REQUEST_METHOD'],
    'get'       => $_GET,
    'post'      => $_POST,
    'request'   => $_REQUEST,
    'headers'   => getallheaders(),
];

// Формируем строку лога
$logLine = json_encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Пишем в лог-файл
file_put_contents($logFile, $logLine, FILE_APPEND);

// Ответим Bitrix24 простым сообщением
echo 'OK';
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html><head>
    <script src="//api.bitrix24.com/api/v1/"></script>
    <script>
        BX24.init(function(){
            BX24.installFinish();
        });
    </script>
</head><body></body></html>
