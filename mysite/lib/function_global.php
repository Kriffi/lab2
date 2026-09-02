<?php
function registrationCorrect($link) { //функция проверки корректности данных формы регистрации
    if ($_POST['login'] == "") return false; //не пусто ли поле логина
    if ($_POST['password1'] == "") return false; //не пусто ли поле пароля
    if ($_POST['password2'] == "") return false; //не пусто ли поле подтверждения пароля
    if (strlen($_POST['password1']) < 5) return false; //не меньше ли 5 символов длина пароля
    if ($_POST['password1'] != $_POST['password2']) return false; //равен ли пароль его подтверждению
    $login = $_POST['login'];
    $rez = mysqli_query($link,"SELECT * FROM users WHERE login='".$login."'");
    if (mysqli_num_rows($rez) != 0) return false; // проверка на существование в БД такого же логина
    return true; //если выполнение функции дошло до этого места, возвращаем true
}

function checkLogin($link) { //функция проверки логина зареганого пользователя
    $login = $_GET['isset_login'];
    $rez = mysqli_query($link,"SELECT * FROM users WHERE login='".$login."'");
    if (mysqli_num_rows($rez) != 0) echo '1';
    else echo '0';
}

function usersTable($link) { //функция отображения данных о зарегистрированных пользователях для админов
    $rez = mysqli_query($link,"SELECT * FROM users");
    $i=0;
    while ($ans = mysqli_fetch_assoc($rez)){
        $i++;
        $t=$ans['reg_date'];
        echo "<tr>
        <td>$i</td>
        <td>".$ans['id']."</td>
        <td>".$ans['login']."</td>
        <td>".date('d.m.Y H:i:s',$t)."</td>
        <td>".$ans['prava']."</td>
        <td>";
        if (!$ans['prava'] || ($ans['id']==$_SESSION['id'])) echo "<form method='post'><input name='id' type='text' value='".$ans['id']."' hidden/><input type='text' name='password2' required/><input type='submit' name='CP' value='Изменить'></form>"; 
        echo "</td>
        <td>";
        //если ты админ - можно удалять других юзеров, но не админов!
        if (!$ans['prava']) echo "<form method='post'><input name='id' type='text' value='".$ans['id']."' hidden/><input type='submit' name='DEL' value='x'/></form>";
        echo "</td>
        </tr>";
    }
}

function enter($link) {
    $error = array(); //массив для ошибок
    if ($_POST['login'] != "" && $_POST['password'] != "") //если поля заполнены
    {
        $login = $_POST['login'];
        $password = $_POST['password'];
        $rez = mysqli_query($link,"SELECT * FROM users WHERE login='".$login."'"); //запрашиваем строку из БД с логином, введённым пользователем
        if (mysqli_num_rows($rez) == 1) //если нашлась одна строка, значит такой юзер существует в БД
        {
            $row = mysqli_fetch_assoc($rez);
            if (md5(md5($password).$row['salt']) == $row['password']) //сравниваем пароль из БД с паролем, введённым пользователем
            {
                //пишем логин и пароль в cookie, также создаём переменную сессии
                // setcookie("login", $row['login'], time() + 50000, '/');
                // setcookie("password", md5($row['login'].$row['password']), time() + 50000, '/'); //проверяем md5 - хэш пароля
                $_SESSION['id'] = $row['id'];          //записываем в сессию id пользователя
                $id = $_SESSION['id'];
                lastAct($id,$link);
                return $error;
            }
            else //если пароли не совпали
            {
                $error[] = "Неверный пароль";
                return $error;
            }
        }
        else //если такого пользователя не найдено в БД
        {
            $error[] = "Неверный логин и пароль";
            return $error;
        }
    }
    else
    {
        $error[] = "Поля не должны быть пустыми!";
        return $error;
    }
}

function login($link) {
    ini_set("session.use_trans_sid", true);
    session_start();
    if (isset($_SESSION['id']))//если сессия есть
    {
        if(isset($_COOKIE['login']) && isset($_COOKIE['password'])) //если cookie есть, то просто обновим время их жизни и вернём true
        {
            SetCookie("login", "", time() - 1, '/');
            SetCookie("password","", time() - 1, '/');
            setcookie("login", $_COOKIE['login'], time() + 50000, '/');
            setcookie("password", $_COOKIE['password'], time() + 50000, '/');
            $id = $_SESSION['id'];
            lastAct($id,$link);
            return true;
        }
        else //иначе добавим cookie с логином и паролем, чтобы после перезапуска браузера сессия не слетала
        {
            $rez = mysqli_query($link,"SELECT * FROM users WHERE id='{$_SESSION['id']}'"); //запрашиваем строку с искомым id
            if (mysqli_num_rows($rez) == 1) //если получена одна строка
            {
                $row = mysqli_fetch_assoc($rez); //записываем её в ассоциативный массив
                setcookie("login", $row['login'], time()+50000, '/');
                setcookie("password", md5($row['login'].$row['password']), time() + 50000, '/');
                $id = $_SESSION['id'];
                lastAct($id,$link);
                return true;
            }
            else return false;
        }
    }
    else //если сессии нет, то проверим существование cookie. Если они существуют, то проверим их валидность по БД
    {
        if(isset($_COOKIE['login']) && isset($_COOKIE['password'])) //если куки существуют.
        {
            $rez = mysqli_query($link,"SELECT * FROM users WHERE login='{$_COOKIE['login']}'"); //запрашиваем строку с искомым логином и паролем
            $row = mysqli_fetch_assoc($rez);
            if(mysqli_num_rows($rez) == 1 && md5($row['login'].$row['password']) == $_COOKIE['password']) //если логин и пароль нашлись в БД
            {
                $_SESSION['id'] = $row['id']; //записываем в сессию id
                $id = $_SESSION['id'];
                lastAct($id,$link);
                return true;
            }
            else //если данные из cookie не подошли, то удаляем эти куки
            {
                SetCookie("login", "", time() - 360000, '/');
                SetCookie("password", "", time() - 360000, '/');
                return false;
            }
        }
        else //если куки не существуют
        {
            return false;
        }
    }
}

function lastAct($id, $link) {
    $tm = time();
    mysqli_query($link, "UPDATE users SET online='$tm', last_act='$tm' WHERE id='$id'");
}

function is_admin($id, $link) {
    $rez = mysqli_query($link, "SELECT prava FROM users WHERE id='$id'");
    if (mysqli_num_rows($rez) == 1) {
        $prava = mysqli_fetch_assoc($rez);
        if ($prava['prava'] == 1) return true;
    }
    return false;
}

function out($link) {
    session_start();
    $id = $_SESSION['id'];
    mysqli_query($link, "UPDATE users SET online=0 WHERE id='$id'");
    unset($_SESSION['id']);
    setcookie("login", "", time() - 360000, '/');
    setcookie("password", "", time() - 360000, '/');
    header('Location: /');
}
?>