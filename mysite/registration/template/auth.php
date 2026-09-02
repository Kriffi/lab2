<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>MySite</title>
</head>
<body>
<div>
<?php
// Если пользователь НЕ авторизован, показываем форму
if (!isset($UID)) {
    if (isset($error) && count($error) != 0) echo '<h5>'.$error[0].'</h5>';
    echo '
    <form action="index.php" method="post">
        Логин: <input type="text" name="login" required/>
        Пароль: <input type="password" name="password" required/>
        <input type="submit" value="Войти" name="log_in" />
    </form>
    <a href="http://'.$_SERVER['HTTP_HOST'].'/registration/">Зарегистрироваться</a><br>
    ';
} 
// Если авторизован, подключаем личный кабинет
else {
    include ('main/main.php');
}
?>
</div>
</body>
</html>