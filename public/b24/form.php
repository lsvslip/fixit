<?php
// public/b24/form.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Fixit — Отправка задачи</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 400px; margin: auto; }
        label { display: block; margin-top: 15px; }
        input, textarea, button { width: 100%; padding: 8px; margin-top: 5px; font-size: 1rem; }
        #status { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>

<h1>Отправить задачу в Bitrix24</h1>

<form id="taskForm">
    <label for="title">Название задачи:</label>
    <input type="text" id="title" name="title" required />

    <label for="description">Описание задачи:</label>
    <textarea id="description" name="description" rows="4" required></textarea>

    <button type="submit">Отправить</button>
</form>

<div id="status"></div>

<script>
    document.getElementById('taskForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const statusEl = document.getElementById('status');
        statusEl.textContent = 'Отправляем…';

        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();

        try {
            const resp = await fetch('/api/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    // Если в Laravel включён CSRF, тогда:
                    // 'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                },
                body: JSON.stringify({ title, description })
            });

            const data = await resp.json();
            if (resp.ok && data.success) {
                statusEl.textContent = 'Задача отправлена! ID = ' + data.task_id;
                // Очистим форму
                document.getElementById('taskForm').reset();
            } else {
                statusEl.textContent = 'Ошибка: ' + (data.error || JSON.stringify(data));
            }
        } catch (err) {
            statusEl.textContent = 'Сбой соединения: ' + err.message;
        }
    });
</script>

</body>
</html>
