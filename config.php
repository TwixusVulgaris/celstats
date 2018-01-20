<?php
$database = 'asterisk';
$db_host = '127.0.0.1';
$db_user = 'asterisk';
$dbu_password = 'PbXpAss';
$db_celtable = 'cel';
$db_statstable = 'stats';
$db_userstable = 'statusers';
$internal_exten_length = 4;

$my_connection = mysqli_connect($db_host, $db_user, $dbu_password, $database);
if (mysqli_connect_errno()) {
    printf("Соединение не удалось: %s\n", mysqli_connect_error());
    exit();
}
?>