<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$title       = $_POST['title'] ?? 'Без названи1я';
$desc        = $_POST['description'] ?? '1213';
$userId      = $_POST['user_id'] ?? ''; // замените на актуальный id текущего пользователя

$domain      = 'b24-amepnm.bitrix24.ru';
$auth        = '6a447068007a3ae80079b236000000010000075f7d59f28d7dc380d3c301d4059095de';

$url = "https://$domain/rest/tasks.task.add.json";


// 1. Получаем ID текущего пользователя
$ch = curl_init("https://$domain/rest/user.current.json");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query(['auth' => $auth]),
]);
$res     = curl_exec($ch);
$resErr  = curl_error($ch);
curl_close($ch);

if ($resErr) {
    die("Ошибка при получении user.current: $resErr");
}
$data = json_decode($res, true);
$userId = $data['result']['ID'] ?? null;
if (!$userId) {
    var_dump($data);
    die("Не удалось получить ID пользователя");
}

// 2. Создаём задачу
$postData = [
    'auth'   => $auth,
    'fields' => [
        'TITLE'       => $title,
        'DESCRIPTION' => $desc,
        'RESPONSIBLE_ID' => $userId,
    ],
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($postData),
]);
$response = curl_exec($ch);
$error    = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');
if ($error) {
    echo json_encode(['success' => false, 'error' => $error]);
} else {
    echo $response;
}
