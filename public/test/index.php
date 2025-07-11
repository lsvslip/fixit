<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>TaskCreator</title>
    <script src="https://api.bitrix24.com/api/v1/"></script>
</head>
<body>
<form id="config-form">
    <input type="hidden" name="responsible_id" id="responsible_id">
    <button type="button" id="btn-resp">Выбрать исполнителя</button>
    <span id="span-resp"></span><br>

    <input type="hidden" name="creator_id" id="creator_id">
    <button type="button" id="btn-creator">Выбрать постановщика</button>
    <span id="span-creator"></span><br>

    <button type="submit">Сохранить настройки</button>
</form>
<script>
    BX24.init(()=>{
        document.getElementById('btn-resp').onclick = () => {
            BX24.selectUsers(users => {
                if(users[0]) {
                    document.getElementById('responsible_id').value = users[0].id;
                    document.getElementById('span-resp').innerText = users[0].name;
                }
            });
        };

        document.getElementById('btn-creator').onclick = () => {
            BX24.selectUsers(users => {
                if(users[0]) {
                    document.getElementById('creator_id').value = users[0].id;
                    document.getElementById('span-creator').innerText = users[0].name;
                }
            });
        };

        document.getElementById('config-form').onsubmit = (e) => {
            e.preventDefault();
            const resp = document.getElementById('responsible_id').value;
            const creator = document.getElementById('creator_id').value;
            BX24.callMethod('appOption.set', {
                KEY: 'fiqsit_config',
                VALUE: JSON.stringify({ responsible: resp, creator: creator })
            }, res => {
                if(res.error()) alert('Ошибка сохранения');
                else alert('Настройки сохранены');
                // Можно закрыть окно сразу: BX24.installFinish()
            });
        };
    });
</script>


</body>
</html>
