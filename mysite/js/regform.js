var emptyField = 'Заполните поле!',
    shortLogin = 'Слишком короткий логин!',
    shortPass = 'Слишком короткий пароль',
    notEqualPass = 'Пароли не совпадают!',
    notUniqueLogin = 'Пользователь с таким именем уже зарегистрирован!',
    valid = true;

var req = false;
if (window.XMLHttpRequest)
    req = new XMLHttpRequest();
else if (window.ActiveXObject)
    req = new ActiveXObject("Microsoft.XMLHTTP");

function ge(id) {
    return document.getElementById(id);
}

function isEmptyStr(str) {
    if (str == "") return true;
    var count = 0;
    for (var i = 0; i < str.length; ++i)
        if (str.charAt(i) == " ") ++count;
    return count == str.length;
}

function notValidField(field, str) {
    field.value = str; // Выводим инфу об ошибке в поле
    field.error = true; // Запоминаем, что поле заполнено не верно
    valid = false; // Считаем форму не валидной
    /* Вешаем обработчик события, который будет очищать поле от
       информации об ошибке при фокусе.
       При потере фокуса поля с type="password" меняют type на
       "text", чтобы информация об ошибках не заменялась звёздочками.
       При фокусе на эти поля им необходимо вернуть назад их родной type
    */
    field.onfocus = function () {
        if (field.id == 'pass' || field.id == 're_pass') field.type = 'password';
        if (field.error) field.value = '';
    }
    // Обработчик, который проверяет поле на корректность при потере им фокуса.
    field.onblur = function () {
        if (isEmptyStr(field.value)) {
            notValidField(field, emptyField);
            if (field.id == 'pass' || field.id == 're_pass') field.type = 'text';
        } else {
            field.error = false;
            switch (field.id) {
                case 'login': checkLogin(); break;
            }
        }
    }
}

function checkLogin() {
    /* Логин не может быть короче 4 символов.
       Выводим инфу о том, что логин слишком короткий только если поле
       было заполнено ранее (!login.error).
    */
    var login = ge('login');
    if (login.value.length < 4 && !login.error) {
        notValidField(login, shortLogin);
        valid = false;
    } else if (!login.error) {
        /* Если логин достаточно длинный, то отправляем синхронный запрос
           для проверки его уникальности.
        */
        req.open('GET', 'index.php?isset_login=' + encodeURIComponent(login.value), false);
        req.send();
        if (req.readyState == 4 && req.status == 200) {
            /* Если пользователь с таким логином уже есть, то
               выводим инфу об этом в поле.
            */var emptyField = 'Заполните поле!',
    shortLogin = 'Слишком короткий логин!',
    shortPass = 'Слишком короткий пароль',
    notEqualPass = 'Пароли не совпадают!',
    notUniqueLogin = 'Пользователь с таким именем уже зарегистрирован!',
    valid = true;

var req = false;
if (window.XMLHttpRequest)
    req = new XMLHttpRequest();
else if (window.ActiveXObject)
    req = new ActiveXObject("Microsoft.XMLHTTP");

function ge(id) {
    return document.getElementById(id);
}

function isEmptyStr(str) {
    if (str == "") return true;
    var count = 0;
    for (var i = 0; i < str.length; ++i)
        if (str.charAt(i) == " ") ++count;
    return count == str.length;
}

function notValidField(field, str) {
    field.value = str; // Выводим инфу об ошибке в поле
    field.error = true; // Запоминаем, что поле заполнено не верно
    valid = false; // Считаем форму не валидной
    /* Вешаем обработчик события, который будет очищать поле от
       информации об ошибке при фокусе.
       При потере фокуса поля с type="password" меняют type на
       "text", чтобы информация об ошибках не заменялась звёздочками.
       При фокусе на эти поля им необходимо вернуть назад их родной type
    */
    field.onfocus = function () {
        if (field.id == 'pass' || field.id == 're_pass') field.type = 'password';
        if (field.error) field.value = '';
    }
    // Обработчик, который проверяет поле на корректность при потере им фокуса.
    field.onblur = function () {
        if (isEmptyStr(field.value)) {
            notValidField(field, emptyField);
            if (field.id == 'pass' || field.id == 're_pass') field.type = 'text';
        } else {
            field.error = false;
            switch (field.id) {
                case 'login': checkLogin(); break;
            }
        }
    }
}

function checkLogin() {
    /* Логин не может быть короче 4 символов.
       Выводим инфу о том, что логин слишком короткий только если поле
       было заполнено ранее (!login.error).
    */
    var login = ge('login');
    if (login.value.length < 4 && !login.error) {
        notValidField(login, shortLogin);
        valid = false;
    } else if (!login.error) {
        /* Если логин достаточно длинный, то отправляем синхронный запрос
           для проверки его уникальности.
        */
        req.open('GET', 'index.php?isset_login=' + encodeURIComponent(login.value), false);
        req.send();
        if (req.readyState == 4 && req.status == 200) {
            /* Если пользователь с таким логином уже есть, то
               выводим инфу об этом в поле.
            */
            if (req.responseText == '1') {
                notValidField(login, notUniqueLogin);
                valid = false;
            }
        }
    }
}

function checkPass() {
    var pass = ge('pass'), re_pass = ge('re_pass');
    if (!pass.error && !re_pass.error) {
        // Проверяем пароли на длину и совпадают ли они.
        if (pass.value.length < 5 && pass.value == re_pass.value) {
            notValidField(pass, shortPass);
            notValidField(re_pass, shortPass);
            valid = false;
            pass.type = 'text';
            re_pass.type = 'text';
        } else if (pass.value != re_pass.value) {
            notValidField(pass, notEqualPass);
            notValidField(re_pass, notEqualPass);
            pass.type = 'text';
            re_pass.type = 'text';
            valid = false;
        }
    }
}

function isValidForm() {
    var elementsf = ge('reg_form').elements,
        login = ge('login'),
        pass = ge('pass'),
        re_pass = ge('re_pass');
    valid = true;
    // Проверяем поля с type="password" и type="text" на "заполненность"
    for (var i = 0; i < elementsf.length; ++i) {
        if ((elementsf[i].type == 'text' || elementsf[i].type == 'password') && isEmptyStr(elementsf[i].value)) {
            notValidField(elementsf[i], emptyField);
            elementsf[i].type = 'text';
        }
        if (elementsf[i].error) valid = false;
    }
    checkLogin();
    checkPass();
    return valid;
}
            if (req.responseText == '1') {
                notValidField(login, notUniqueLogin);
                valid = false;
            }
        }
    }
}

function checkPass() {
    var pass = ge('pass'), re_pass = ge('re_pass');
    if (!pass.error && !re_pass.error) {
        // Проверяем пароли на длину и совпадают ли они.
        if (pass.value.length < 5 && pass.value == re_pass.value) {
            notValidField(pass, shortPass);
            notValidField(re_pass, shortPass);
            valid = false;
            pass.type = 'text';
            re_pass.type = 'text';
        } else if (pass.value != re_pass.value) {
            notValidField(pass, notEqualPass);
            notValidField(re_pass, notEqualPass);
            pass.type = 'text';
            re_pass.type = 'text';
            valid = false;
        }
    }
}

function isValidForm() {
    var elementsf = ge('reg_form').elements,
        login = ge('login'),
        pass = ge('pass'),
        re_pass = ge('re_pass');
    valid = true;
    // Проверяем поля с type="password" и type="text" на "заполненность"
    for (var i = 0; i < elementsf.length; ++i) {
        if ((elementsf[i].type == 'text' || elementsf[i].type == 'password') && isEmptyStr(elementsf[i].value)) {
            notValidField(elementsf[i], emptyField);
            elementsf[i].type = 'text';
        }
        if (elementsf[i].error) valid = false;
    }
    checkLogin();
    checkPass();
    return valid;
}