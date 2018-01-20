#!/usr/bin/php

<?php
require 'config.php';
$dbConn = new mysqli($db_host, $db_user, $dbu_password, $database);
if ($dbConn->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $dbConn->connect_errno . ") " . $dbConn->connect_error;
}
$unpricedArr = array();
$query = "SELECT id, talkduration, provider, code, price, value FROM {$db_statstable} WHERE provider != '' AND price = 0;";
$res = $dbConn->query($query);
while ($unpriced = $res->fetch_assoc()) {
	$unpricedArr[] = $unpriced;
}
unset($unpriced);
$res->close();
foreach ($unpricedArr as $row) {
	$query = "SELECT Prices.Price FROM Prices INNER JOIN sipProvider WHERE Prices.Code = '{$row['code']}' AND Prices.ProviderID = sipProvider.id AND sipProvider.name = '{$row['provider']}';";
	$res = $dbConn->query($query);
	$priceArr = array();
	while ($price = $res->fetch_assoc()) {
	    $priceArr[] = $price;
    }
    $res->close();
    $row['price'] = (float) $priceArr[0]['Price'];
    $row['value'] = $row['price'] * ceil((float) $row['talkduration'] / 60);
	$query = "UPDATE {$db_statstable} SET price = '{$row['price']}', value = '{$row['value']}' WHERE id = '{$row['id']}';";
	var_dump($query);
	$res = $dbConn->query($query);
	var_dump($res);
}
unset($row);
$dbConn->close();
?>