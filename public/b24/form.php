<?php
// public/b24/form.php
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Fixit — Отправка задачи</title>
    <style>
        body {
            background: #f3f4f6;
            color: #333;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto,
                'Helvetica Neue', Arial, sans-serif;
        }

        .box {
            background: #fff;
            padding: 30px;
            max-width: 480px;
            margin: 60px auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
        }

        input,
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            font-size: 1rem;
            color: #fff;
            background-color: #1976d2;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #1565c0;
        }

        #status {
            margin-top: 20px;
            font-weight: bold;
        }

        #status.success {
            color: #3c763d;
        }

        #status.error {
            color: #a94442;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>Отправить задачу в Bitrix24</h1>

    <form id="taskForm">
        <label for="title">Название задачи:</label>
        <input type="text" id="title" name="title" required />

        <label for="description">Описание задачи:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <button type="submit">Отправить</button>
    </form>

    <div id="status"></div>
</div>

<script>
    document.getElementById('taskForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const statusEl = document.getElementById('status');
        statusEl.textContent = 'Отправляем…';
        statusEl.className = '';

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
                statusEl.className = 'success';
                // Очистим форму
                document.getElementById('taskForm').reset();
            } else {
                statusEl.textContent = 'Ошибка: ' + (data.error || JSON.stringify(data));
                statusEl.className = 'error';
            }
        } catch (err) {
            statusEl.textContent = 'Сбой соединения: ' + err.message;
            statusEl.className = 'error';
        }
    });
</script>

</body>
</html>
