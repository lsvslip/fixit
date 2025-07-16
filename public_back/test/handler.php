<input type="hidden" name="responsible" id="responsible" value="">
<input type="hidden" name="created_by" id="created_by" value="">

<button type="button" id="pickResponsible">Выбрать исполнителя</button>
<span id="responsibleName"></span><br>

<button type="button" id="pickCreator">Выбрать постановщика</button>
<span id="createdByName"></span><br>
<script>
    BX24.init(() => {
        document.getElementById('pickResponsible').onclick = function() {
            BX24.selectUsers(users => {
                if (users.length) {
                    document.getElementById('responsible').value = users[0].id;
                    document.getElementById('responsibleName').innerText = users[0].name;
                }
            });
        };

        document.getElementById('pickCreator').onclick = function() {
            BX24.selectUsers(users => {
                if (users.length) {
                    document.getElementById('created_by').value = users[0].id;
                    document.getElementById('createdByName').innerText = users[0].name;
                }
            });
        };
    });
</script>
