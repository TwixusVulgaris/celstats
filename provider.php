#!/usr/bin/php

<?php

function SearchCheapest($number, $DB){
    $result['provider'] = 'default';
// Поскольку самый короткий код в базе - из одного символа, берём первый символ номера и ищем в базе все коды, начинающиеся с него,
// для номеров РФ (начинаются с 7) берем в качестве минимального кода первые 4 символа
    $code = '';
    if ($number[0] = '7'){$code = substr($number, 0, 4) . "%";}
    else {$code = substr($number, 0, 1) . "%";}
    $Query = "SELECT Prices.Region, Prices.Code, Prices.Price, sipProvider.name AS ProvName FROM Prices INNER JOIN sipProvider WHERE Prices.Code LIKE '{$code}' AND Prices.ProviderID = sipProvider.id AND sipProvider.isRegistred = '1'";
    $res = $DB->query($Query);
// Записываем результат запроса в массив $Variants[]
    $Variants = array();
    $res->data_seek(0);
    while ($row = $res->fetch_assoc()) {
        $Variants[] = $row;
    }
    $res->close();
// Находим самый длинный код, который присутствует в номере.
    $MaxCodeLen = 0;
    $selected = array();
    foreach ($Variants as $variant){
        $len = strlen($variant['Code']);
        if ($len >= $MaxCodeLen && $variant['Code'] == substr($number, 0, $len)) {
            if ($len > $MaxCodeLen) {
                $selected = array();
                $MaxCodeLen = $len;
            }
            $selected[] = $variant;
        }
    }
// Находим оператора с минимальной ценой минуты
    $MinPrice = $selected[0]['Price'] + 10;
    foreach ($selected as $item){
        if ((float)$item['Price'] < $MinPrice) {
            $MinPrice = $item['Price'];
            $result['provider'] = $item['ProvName'];
            $result['code'] = $item['Code'];
            $result['price'] = $MinPrice;
        }
    }
    return($result);
}

function PrepareNumber($num, $prov){
    switch ($prov) {
        case 'Spacetel':
            $num[0] = '7';
            break;
        default:
            $num[0] = '8';
            break;
    }
    return($num);
}

require('phpagi/phpagi.php');
$agi = new AGI();
$admins = 'twixenator@gmail.com';
$DbConn = new mysqli('localhost', 'asterisk', 'PbX71pAss', 'asterisk');
$Dialed = (string)$argv[1];
$provider = array();
$ForDial = '';
var_dump($Dialed);
if (strlen($Dialed) == 7) {
    $ForDial = '7495' . $Dialed;
    $provider = SearchCheapest($ForDial, $DbConn);
    $ForDial = PrepareNumber($ForDial, $provider['provider']);
}
if (substr($Dialed,0,4) == '8800') {
    $query = "SELECT settings.value FROM settings WHERE settings.param = 'default_channel_msk';";
    $qres = $DbConn->query($query);
    $row = $qres->fetch_assoc();
    $qres->close();
    $provider['provider'] = $row['value'];
    $provider['code'] = '8800';
    $provider['price'] = '0';
    $ForDial = PrepareNumber($Dialed, $provider['provider']);
}
if (substr($Dialed, 0, 3) == '810') {
    $ForDial = substr($Dialed,3);
    $provider = SearchCheapest($ForDial, $DbConn);
    $ForDial = $Dialed;
}
if ($Dialed[0] == '8' && $Dialed[1] != '1' && substr($Dialed,0,4) != '8800'){
    $ForDial = $Dialed;
    $ForDial[0] = '7';
    $provider = SearchCheapest($ForDial, $DbConn);
    $ForDial = PrepareNumber($ForDial, $provider['provider']);
}
if ($provider['provider'] == 'default') {
    $query = "SELECT settings.value FROM settings WHERE settings.param = 'default_channel_msk';";
    $qres = $DbConn->query($query);
    $row = $qres->fetch_assoc();
    $qres->close();
    $provider['provider'] = $row['value'];
    $provider['code'] = '';
    $provider['price'] = '0';
    $sent = "notSent";
    $warnMessage = "Not found code for dialed number '{$Dialed}'. Call will be sent to default provider ('{$provider['provider']}')!";
    $warnMessage = wordwrap($warnMessage, 70, "\r\n");
    $sent = mail($admins, 'Code not found in Prices table!', $warnMessage);
}
//var_dump($provider);
$agi->set_variable('prep_exten', $ForDial);
$agi->set_variable('provider', $provider['provider']);
$agi->set_variable('finalcode', $provider['code']);
$agi->set_variable('price', $provider['price']);
?>