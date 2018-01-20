#!/usr/bin/php
<?php
$admins = 'reports@ariel.ru;twixenator@gmail.com';
$DbConn = new mysqli('localhost', 'asterisk', 'PbX71pAss', 'asterisk');

$registrations = array();
$lines = file('/root/bin/registry');
$keys = preg_split("/[\s]+/", trim($lines[0]));
$keysCount = count($keys);
array_pop($lines);
array_shift($lines);
$toSearch = array('No Authentication', 'Request Sent', 'voip.mtt.ru:5060', '46.183.164.9:5060', '192.168.20.51:5060');
$toReplace = array('No_Authentication', 'Request_Sent', 'MTT', 'Spacetel', 'MCM');
foreach ($lines as $line) {
	$line = trim(str_replace($toSearch, $toReplace, $line));
	$fields = preg_split('/[\s]+/', $line, $keysCount);
	$newline = array();
	foreach ($fields as $key => $value) {
		$newline[$keys[$key]] = $value;
	}
	$registrations[] = $newline;
}
foreach ($registrations as $reg) {
	if ($reg['State'] <> 'Registered') {
		$sent = "notSent";
		$warnMessage = "Trunk to provider '{$reg['Host']}' with username '{$reg['Username']}' is not registered! Current status is '{$reg['State']}' Updating status in database will be performed immediately!";
                $warnMessage = wordwrap($warnMessage, 70, "\r\n");
		$sent = mail($admins, 'WARNING! Unregistered trunk', $warnMessage);
		$query = "UPDATE sipProvider SET IsRegistred = '0' WHERE name = '{$reg['Host']}'";
		$DbConn->query($query);
	} else {
		$query = "UPDATE sipProvider SET IsRegistred = '1' WHERE name = '{$reg['Host']}'";
		$DbConn->query($query);
	}
}
?>
