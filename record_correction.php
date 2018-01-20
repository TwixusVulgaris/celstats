#!/usr/bin/php

<?php
require 'config.php';
$dbConn = new mysqli($db_host, $db_user, $dbu_password, $database);
if ($dbConn->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $dbConn->connect_errno . ") " . $dbConn->connect_error;
}
$callsArr = array();
$query = "SELECT * FROM {$db_statstable} WHERE recordfile != '' LIMIT 45000;";
$recordedCalls = $dbConn->query($query);
var_dump($recordedCalls);
while ($recordedCall = $recordedCalls->fetch_assoc()) {
	var_dump($recordedCall);
	$record = explode(',', $recordedCall['recordfile']);
	$recordedCall['recordfile'] = str_replace('wav', 'mp3', $record[0]);
	$recordedCall['recordfile'] = str_replace('//', '/', $recordedCall['recordfile']);
	$callsArr[] = $recordedCall;
}
//var_dump($callsArr);
$recordedCalls->close();
foreach ($callsArr as $call) {
	$query = "UPDATE {$db_statstable} SET {$db_statstable}.recordfile = '{$call['recordfile']}' WHERE {$db_statstable}.uniqueid = '{$call['uniqueid']}'";
	var_dump($query);
	$inserted = $dbConn->query($query);
	var_dump($inserted);
}
?>