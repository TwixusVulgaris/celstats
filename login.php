
<?php
require 'config.php';
require 'functions.php';
session_start();
if ($_SESSION['userid']) {
  if ($_REQUEST['logout']) {
    $_SESSION['userid'] = 0;
    print "До свидания!";
    exit();
  }
} else {
  if ($_POST['login'] && $_POST['password']) {
    $userid = find_userid($_POST['login'], $_POST['password']);
    if ($userid) {
      $_SESSION['userid'] = $userid;
//      print($_SESSION['userid']);
    } else {
      print "Неверный логин или пароль!";
      print_login_form();
      exit();
    }
  } else {
    print('Пожалуйста, авторизуйтесь');
    print_login_form();
    exit();
  }
}
?>