<?php
ini_set("session.use_trans_sid", true);
session_start();
include ('../lib/connect.php'); //подключаемся к БД
include ('../lib/function_global.php'); //подключаем библиотеку функций

if (isset($_POST['GO'])) //если была нажата кнопка регистрации, проверим данные на корректность и, если данные введены и введены правильно,
                         //добавим запись с новым пользователем в БД
{
    $correct = registrationCorrect($link); //записываем в переменную результат работы функции registrationCorrect(), которая возвращает true, если введённые данные верны и
    if ($correct) //если данные верны, запишем их в базу данных
    {
        $login = htmlspecialchars($_POST['login']);
        $password = $_POST['password1'];
        if (isset($_POST['adm'])) 
            $adm=1; else $adm=0;
        $salt = mt_rand(100, 999);
        $tm = time();
        $password = md5(md5($password).$salt);
        
        if (mysqli_query($link,"INSERT INTO users (login,password,salt,prava,last_act,online,reg_date) VALUES ('".$login."','".$password."','".$salt."','".$adm."',0,0,'".$tm."')"))
        { //пишем данные в БД и авторизовываем пользователя
            $regged = true;
            include ("template/registration.php"); //подключаем шаблон
        }
    }
    else
    {
        $regged_error=true;
        include_once ("template/registration.php"); //подключаем шаблон в случае некорректности данных
    }
}
else
{
    if (isset($_GET['isset_login']))
        checkLogin($link);
    else
        include_once ("template/registration.php"); //подключаем шаблон в случае если кнопка регистрации нажата не была, то есть, пользователь только перешёл на страницу
                                                    //регистрации
}
?>