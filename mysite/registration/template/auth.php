
<form action="index.php" method="post">
    Логин: <input type="text" name="login" required/>
    Пароль: <input type="password" name="password" required/>
    <input type="submit" value="Войти" name="log_in" />
</form>
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/registration/">Зарегистрироваться</a><br>