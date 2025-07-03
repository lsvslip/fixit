<?php
$domain = $_REQUEST['DOMAIN'] ?? '';
$keyFile = __DIR__ . "/keys/{$domain}.key";

$apiKey = ($domain && file_exists($keyFile))
    ? file_get_contents($keyFile)
    : 'Ключ ещё не сгенерирован';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fixit — API-ключ</title>
    <style>body{font-family:sans-serif;padding:20px;}code{background:#f4f4f4;padding:2px 4px;border-radius:4px;}</style>
</head>
<body>
<h1>Fixit — API-ключ</h1>
<p><strong>Портал:</strong> <code><?=htmlspecialchars($domain)?></code></p>
<p><strong>API-ключ:</strong> <code><?=htmlspecialchars($apiKey)?></code></p>
<p style="color:#888;font-size:.9em;">Ключ генерируется один раз при установке приложения.</p>
</body>
</html>
