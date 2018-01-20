#!/usr/bin/php

<?php
require 'config.php';
require 'functions.php';

$dbConn = new mysqli($db_host, $db_user, $dbu_password, $database);
if ($dbConn->connect_errno) {
    echo "Не удалось подключиться к MySQL: (" . $dbConn->connect_errno . ") " . $dbConn->connect_error;
}
//Извлечение и обработка данных CEL 
$callFlowsArr = array();
$query = "SELECT {$db_celtable}.*, UNIX_TIMESTAMP(eventtime) as evtime FROM {$db_celtable} LEFT JOIN {$db_statstable} ON {$db_celtable}.linkedid = {$db_statstable}.uniqueid WHERE {$db_statstable}.uniqueid IS NULL LIMIT 45000";
$newCalls = $dbConn->query($query);
$bridgeEvents = array('BRIDGE_ENTER' => "BRIDGE_EVENT", 'BRIDGE_EXIT' => "BRIDGE_EVENT");
while ($celEvent = $newCalls->fetch_assoc()) {
	$evType = $celEvent['eventtype'];
	if(array_key_exists($evType, $bridgeEvents)){
		$evType = $bridgeEvents[$evType];
	}
	$callFlowsArr[$celEvent['linkedid']][$evType][] = $celEvent;
}
$statsDataToWrite = array();
foreach ($callFlowsArr as $callFlow) {
	$statsRecordArr = array();
	if (array_key_exists('LINKEDID_END', $callFlow)) {
		$statsRecordArr['uniqueid'] = $callFlow['CHAN_START'][0]['linkedid'];
		if ($callFlow['CHAN_START'][0]['cid_num']) {
		    $statsRecordArr['callerid'] = $callFlow['CHAN_START'][0]['cid_num'];
		} else {
			$temp = explode('/', $callFlow['CHAN_START'][0]['channame']);
			$temp = explode('-', $temp[1]);
			$statsRecordArr['callerid'] = $temp[0];
		}
		if (array_key_exists('APP_START', $callFlow)) {
			$statsRecordArr['visible'] = 1;
			$statsRecordArr['starttime'] = $callFlow['CHAN_START'][0]['eventtime'];
			$statsRecordArr['fullduration'] = $callFlow['LINKEDID_END'][0]['evtime'] - $callFlow['CHAN_START'][0]['evtime'];
			if (array_key_exists('BRIDGE_EVENT', $callFlow)) {
				$statsRecordArr['status'] = 'answered';
				if ($callFlow['BRIDGE_EVENT'][1]['cid_num']) {
				    $statsRecordArr['answered'] = $callFlow['BRIDGE_EVENT'][1]['cid_num'];
				} else {
	                $answ = explode('/', $callFlow['BRIDGE_EVENT'][0]['peer']);
	                $answ = explode('-', $answ[1]);
	                $statsRecordArr['answered'] = $answ[0];
				}
				$statsRecordArr['talkduration'] = $callFlow['LINKEDID_END'][0]['evtime'] - $callFlow['BRIDGE_EVENT'][0]['evtime'];
			} else {
				$statsRecordArr['status'] = 'lost';
			}
			if (strlen($statsRecordArr['callerid']) > $internal_exten_length) {
				$statsRecordArr['direction'] = 'inbound';
				if ($statsRecordArr['status'] == 'answered') {
				    if ($callFlow['BRIDGE_EVENT'][0]['appname'] == 'Queue') {
					    $appDataArr = explode(',', ($callFlow['BRIDGE_EVENT'][0]['appdata']));
					    $statsRecordArr['inqueue'] = $appDataArr[0];
				    }
				    if ($callFlow['APP_START'][2]['appname'] == 'Dial') {
				    	$statsRecordArr['ivrdialed'] = $callFlow['APP_START'][2]['exten'];
				    } elseif ($callFlow['APP_START'][3]['appname'] == 'Dial') {
				    	$statsRecordArr['ivrdialed'] = $callFlow['APP_START'][3]['exten'];
				    }
				    if (count($callFlow['ATTENDEDTRANSFER'])) {
				    	$countTansfers = count($callFlow['ATTENDEDTRANSFER']);
					    if ($callFlow['BRIDGE_EVENT'][0]['extra']) {
					    	$mainBridge = '';
					    	$transferArr = array();
					    	foreach ($callFlow['BRIDGE_EVENT'] as $bridgeEvt) {
					    		if($bridgeEvt['eventtype'] == "BRIDGE_ENTER") {
					    			$brExtra = json_decode($bridgeEvt['extra'], TRUE);
					    			if(!$mainBridge){
					    			    $mainBridge = $brExtra['bridge_id'];
					    			} elseif($brExtra['bridge_id'] == $mainBridge) {
					    				    $transferArr[] = $bridgeEvt['cid_num'];
					    			} 
					    		} 
					    	}
					    	if ($statsRecordArr['answered'] == $transferArr[0]) {
				                array_shift($transferArr);
					    	}
				            $transferArr = array_unique($transferArr);
					    	$statsRecordArr['transferredto'] = implode(',', $transferArr);
				        } else {
				    	    $statsRecordArr['transferredto'] = "Transferred";
				        }
				        // var_dump($statsRecordArr['uniqueid']);
				        // var_dump($statsRecordArr['answered']);
				        // var_dump($statsRecordArr['transferredto']);
					}
				} else {
					if ($callFlow['APP_START'][4]['appname'] == 'Queue') {
					    $appDataArr = explode(',', ($callFlow['APP_START'][4]['appdata']));
					    $statsRecordArr['inqueue'] = $appDataArr[0];
					}
				}
			} elseif (strlen($callFlow['CHAN_START'][0]['exten']) > $internal_exten_length) {
				$statsRecordArr['direction'] = 'outbound';
				$statsRecordArr['answered'] = $callFlow['CHAN_START'][0]['exten'];
			 	$statsRecordArr['exten'] = $callFlow['CHAN_START'][0]['exten'];
			 	$index = find_idx($callFlow['APP_START'], 'appname', 'Dial');
			 	var_dump($callFlow['CHAN_START'][0]['eventtime']);
			 	var_dump($callFlow['CHAN_START'][0]['linkedid']);
			 	var_dump($index);
		 		$params = explode(",", $callFlow['APP_START'][$index]['userfield'], 3);
		 		$statsRecordArr['code'] = $params[0];
		 		$statsRecordArr['provider'] = $params[1];
		 		$statsRecordArr['price'] = $params[2];
			 	if ($statsRecordArr['status'] == 'answered') {
			 		$statsRecordArr['value'] = (float) $statsRecordArr['price'] * ceil((float) $statsRecordArr['talkduration'] / 60);
			 	}
			} else {
				$statsRecordArr['direction'] = 'internal';
			 	$statsRecordArr['exten'] = $callFlow['CHAN_START'][0]['exten'];
			 	if ($statsRecordArr['status'] == 'answered') {
			 		if (array_key_exists('PICKUP', $callFlow)) {
			 			$param = end($callFlow['BRIDGE_EVENT']);
			 			$statsRecordArr['answered'] = $param['cid_num'];
			 		}
			 		// } else {
			 		//     $statsRecordArr['answered'] = $callFlow['ANSWER'][0]['cid_num'];
			 		// }
			 	}
			}
			foreach ($callFlow['APP_START'] as $call) {
				if ($call['appname'] == 'MixMonitor') {
					$appDataArr = explode(',', $call['appdata']);
					$statsRecordArr['recordfile'] = str_replace('wav', 'mp3', $appDataArr[0]);
				}
			}
		} else {
			$statsRecordArr['visible'] = 0;
		}
		$statsDataToWrite[] = $statsRecordArr;
	}
}
$newCalls->close();
//Запись обработанных данных в базу.
$fields = array("uniqueid", "direction", "status", "starttime", "callerid", "exten", "answered", "transferredto", "recordfile", "ivrdialed", "fullduration", "talkduration", "provider", "code", "price", "value", "inqueue", "visible");
foreach ($statsDataToWrite as $recordToInsert) {
	for ($i=0; $i < count($fields); $i++) { 
		if (!array_key_exists($fields[$i], $recordToInsert)) {
			$recordToInsert[$fields[$i]] = "";
		}
	}
	$query = "INSERT INTO {$db_statstable} (uniqueid, direction, status, starttime, callerid, exten, answered, transferredto, recordfile, ivrdialed, fullduration, talkduration, provider, code, price, value, inqueue, visible) VALUES ('{$recordToInsert['uniqueid']}','{$recordToInsert['direction']}', '{$recordToInsert['status']}', '{$recordToInsert['starttime']}', '{$recordToInsert['callerid']}', '{$recordToInsert['exten']}', '{$recordToInsert['answered']}', '{$recordToInsert['transferredto']}', '{$recordToInsert['recordfile']}', '{$recordToInsert['ivrdialed']}', '{$recordToInsert['fullduration']}', '{$recordToInsert['talkduration']}', '{$recordToInsert['provider']}', '{$recordToInsert['code']}', '{$recordToInsert['price']}', '{$recordToInsert['value']}', '{$recordToInsert['inqueue']}', '{$recordToInsert['visible']}');";
	$inserted = $dbConn->query($query);
}
$dbConn->close();
$logFileRecord = date('d.m.Y G:i:s') . "\n";
file_put_contents('/var/www/html/celstats.auth/db_processing.log', $logFileRecord);
?>