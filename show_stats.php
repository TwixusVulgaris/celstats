<?php
require 'config.php';
require 'functions.php';

include 'form_stats.php';

//Обрабатываем данные из формы
$filters_arr = array();
$generate_csv = false;
$csv_file_name = "";
if (!empty($_POST['filter'])) {
    if ($_POST['startdate']) {
        if ($_POST['starttime']){
            $filters_arr['starttime'] = $_POST['startdate'] . "T";
            $filters_arr['starttime'] .= $_POST['starttime'];
        } else { $filters_arr['starttime'] = $_POST['startdate'] . "T0:0";}
    } else {
        if ($_POST['starttime']){
            $filters_arr['starttime'] = date('Y-m-d') . "T";
            $filters_arr['starttime'] .= $_POST['starttime'];
        } else { $filters_arr['starttime'] = date('Y-m-d') . "T0:0";}
    }
    if ($_POST['enddate']) {
        if ($_POST['endtime']){
            $filters_arr['endtime'] = $_POST['enddate'] . "T";
            $filters_arr['endtime'] .= $_POST['endtime'];
        } else { $filters_arr['endtime'] = $_POST['enddate'] . "T23:59";}
    } else {
        if ($_POST['endtime']){
            $filters_arr['endtime'] = date('Y-m-d') . "T";
            $filters_arr['endtime'] .= $_POST['endtime'];
        } else { $filters_arr['endtime'] = date('Y-m-d') . "T23:59";}
    }
    switch ($_POST['direction']) {
        case 1:
            $filters_arr['direction'] = 'inbound';
            break;
        case 2:
            $filters_arr['direction'] = 'outbound';
            break;
        case 3:
            $filters_arr['direction'] = 'internal';
            break;
        default:
            break;
    }
    switch ($_POST['status']) {
        case 1:
            $filters_arr['status'] = 'answered';
            break;
        case 2:
            $filters_arr['direction'] = 'lost';
            break;
        default:
            break;
    }
    if ($_POST['answered']) {$filters_arr['answered'] = $_POST['answered'];}
    if ($_POST['answ_cond']) {$filters_arr['answ_cond'] = $_POST['answ_cond'];}
    if ($_POST['callerid']) {$filters_arr['callerid'] = $_POST['callerid'];}
    if ($_POST['cid_cond']) {$filters_arr['cid_cond'] = $_POST['cid_cond'];}
    if ($_POST['exten']) {$filters_arr['exten'] = $_POST['exten'];}
    if ($_POST['ext_cond']) {$filters_arr['ext_cond'] = $_POST['ext_cond'];}
    switch ($_POST['ivrdialed']) {
        case 1:
            $filters_arr['ivrdialed'] = 1;
            break;
        case 2:
            $filters_arr['ivrdialed'] = 2;
            break;
        case 3:
            $filters_arr['ivrdialed'] = 3;
            break;
        case "t":
            $filters_arr['ivrdialed'] = "t";
            break;
        case "internal":
            $filters_arr['ivrdialed'] = "internal";
            break;
        default:
            break;
    }
    switch ($_POST['sort_field']){
        case "date":
            $filters_arr['sort_field'] = 'starttime';
            break;
        case "cid":
            $filters_arr['sort_field'] = 'callerid';
            break;
        case "exten":
            $filters_arr['sort_field'] = 'exten';
            break;
        case "ivrd":
            $filters_arr['sort_field'] = 'ivrdialed';
            break;
        case "dir":
            $filters_arr['sort_field'] = 'direction';
            break;
        default:
            $filters_arr['sort_field'] = 'id';
            break;
    }
    switch ($_POST['sort_type']) {
        case "asc":
            $filters_arr['sort_type'] = 'ASC';
            break;
        case "desc":
            $filters_arr['sort_type'] = 'DESC';
            break;
        default:
            $filters_arr['sort_type'] = 'ASC';
            break;
    }
    if ($_POST['need_csv']) {$generate_csv = true;}
    $filters_arr['limit'] = $_POST['limit'];
 // Создаём запрос
    $query = "SELECT * FROM {$db_statstable} ";
    $condition = "";
    if (array_key_exists('starttime', $filters_arr)){
        if(array_key_exists('endtime', $filters_arr)){
            $condition = addFilterCondition($condition,"starttime BETWEEN '{$filters_arr['starttime']}' AND '{$filters_arr['endtime']}' ");
        } else {
            $condition = addFilterCondition($condition,"starttime >= '{$filters_arr['starttime']}' ");
        }
    } elseif (array_key_exists('endtime', $filters_arr)) {
        $condition = addFilterCondition($condition,"starttime <= '{$filters_arr['endtime']}' ");
    }
    if (array_key_exists('direction', $filters_arr)){
        $condition = addFilterCondition($condition,"direction = '{$filters_arr['direction']}' ");
    }
    if (array_key_exists('status', $filters_arr)) {
        $condition = addFilterCondition($condition, "status = '{$filters_arr['status']}' ");
    }
    if (array_key_exists('callerid', $filters_arr)) {
        switch ($filters_arr['cid_cond']){
            case "begins":
                $condition = addFilterCondition($condition, "callerid LIKE '{$filters_arr['callerid']}%' ");
                break;
            case "ends":
                $condition = addFilterCondition($condition, "callerid LIKE '%{$filters_arr['callerid']}' ");
                break;
            case "contains":
                $condition = addFilterCondition($condition, "callerid LIKE '%{$filters_arr['callerid']}%' ");
                break;
            case "equal":
                $condition = addFilterCondition($condition, "callerid = '{$filters_arr['callerid']}' ");
                break;
            default:
                $condition = addFilterCondition($condition, "callerid LIKE '{$filters_arr['callerid']}%' ");
                break;
        }
    }
    if (array_key_exists('exten', $filters_arr)) {
        switch ($filters_arr['ext_cond']){
            case "begins":
                $condition = addFilterCondition($condition, "exten LIKE '{$filters_arr['exten']}%' ");
                break;
            case "ends":
                $condition = addFilterCondition($condition, "exten LIKE '%{$filters_arr['exten']}' ");
                break;
            case "contains":
                $condition = addFilterCondition($condition, "exten LIKE '%{$filters_arr['exten']}%' ");
                break;
            case "equal":
                $condition = addFilterCondition($condition, "exten = '{$filters_arr['exten']}' ");
                break;
            default:
                $condition = addFilterCondition($condition, "exten LIKE '{$filters_arr['exten']}%' ");
                break;
        }
    }
    if (array_key_exists('ivrdialed', $filters_arr)){
        switch ($filters_arr['ivrdialed']) {
            case 1:
                $condition = addFilterCondition($condition, "ivrdialed = '{$filters_arr['ivrdialed']}' ");
                break;
            case 2:
                $condition = addFilterCondition($condition, "ivrdialed = '{$filters_arr['ivrdialed']}' ");
                break;
            case 3:
                $condition = addFilterCondition($condition, "ivrdialed = '{$filters_arr['ivrdialed']}' ");
                break;
            case "t":
                $condition = addFilterCondition($condition, "ivrdialed = '{$filters_arr['ivrdialed']}' ");
                break;
            case "internal":
                $condition = addFilterCondition($condition, "ivrdialed LIKE '___' ");
                break;
            default:
                break;
        }
    }
    if (array_key_exists('answered', $filters_arr)) {
        switch ($filters_arr['answ_cond']){
            case "begins":
                $condition = addFilterCondition($condition, "answered LIKE '{$filters_arr['answered']}%' ");
                break;
            case "ends":
                $condition = addFilterCondition($condition, "answered LIKE '%{$filters_arr['answered']}' ");
                break;
            case "contains":
                $condition = addFilterCondition($condition, "answered LIKE '%{$filters_arr['answered']}%' ");
                break;
            case "equal":
                $condition = addFilterCondition($condition, "answered = '{$filters_arr['answered']}' ");
                break;
            default:
                $condition = addFilterCondition($condition, "answered LIKE '{$filters_arr['answered']}%' ");
                break;
        }
    }

    if ($condition) {$query .= "WHERE {$condition} ";}
    if ($filters_arr['sort_field'] && $filters_arr['sort_type']) {$query .= " AND visible = 1 ORDER BY {$filters_arr['sort_field']} {$filters_arr['sort_type']} ";}
    $query .= " LIMIT {$filters_arr['limit']}";
    //print_r($query);
    //var_dump($query);
    //Осуществляем запрос к БД
    $filtered_calls = mysqli_query($my_connection, $query);
    $filtered_calls_arr = array();
    $ans_calls = 0;
    $unans_calls = 0;
    while ($call_s = mysqli_fetch_assoc($filtered_calls)) {
        $filtered_calls_arr[] = $call_s;
    }
    //Подсчитываем общее количество отобранных звонков, и сколько среди них отвеченных/неотвеченных
    $whole_calls = count($filtered_calls_arr);
    foreach ($filtered_calls_arr as $calld){
        if ($calld['status'] == "answered") {$ans_calls++;}
        else {$unans_calls++;}
    }
    //Создаём csv если было запрошено
    if ($generate_csv) {
        $csv_file_name = generateCsv($filtered_calls_arr);
    }
    $processDate = file_get_contents('/var/www/html/celstats.auth/db_processing.log');
    //Выводим результат
    ?>
    <table>
        <tr>
            <td class="statistic_all">Всего звонков отобрано: <?php echo " {$whole_calls}";?></td>
            <td class="statistic_answ">Отвеченных: <?php echo " {$ans_calls}";?></td>
            <td class="statistic_noansw">Неотвеченных: <?php echo " {$unans_calls}";?></td>
            <td>Последняя обработка: <?php echo " {$processDate}"; ?></td>
            <td></td>
            <?php if ($generate_csv) {echo "<td><a href=\"downloadcsv.php?csv={$csv_file_name}\">Нажмите, чтобы скачать CSV</a></td>";} ?>
        </tr>
    </table>
    <table width="100%">
      <thead>
        <tr>
          <th></th>
          <th>Дата</th>
          <th>Звонил</th>
          <th>Набранный номер</th>
          <th>Набрано в IVR</th>
          <th>Ответил</th>
          <th>Переведён на</th>
          <th>Полная длительность (с)</th>
          <th>Длительность разговора (с)</th>
          <th>Провайдер</th>
          <th>Цена (&#8381;)</th>
          <th>Стоимость (&#8381;)</th>  
          <th>Запись разговора</th>
          <th>"Сырые" данные</th>
        </tr>
      </thead>
      <tbody>
    <?php
    $rowsCount = 0;
    foreach ($filtered_calls_arr as $call)
    {
        if ( $rowsCount && !($rowsCount % 25)){
            echo '<tr>';
            echo '<th></th>';
            echo '<th>Дата</th>';
            echo '<th>Звонил</th>';
            echo '<th>Набранный номер</th>';
            echo '<th>Набрано в IVR</th>';
            echo '<th>Ответил</th>';
            echo '<th>Переведён на</th>';
            echo '<th>Полная длительность (с)</th>';
            echo '<th>Длительность разговора (с)</th>';
            echo '<th>Провайдер</th>';
            echo '<th>Цена (&#8381;)</th>';
            echo '<th>Стоимость (&#8381;)</th>  ';
            echo '<th>Запись разговора</th>';
            echo '<th>"Сырые" данные</th>';
            echo '</tr>';
        }
        $ivrdialed = "";
        $icon = "icons/";
        switch ($call['direction']) {
            case "inbound":
                if ($call['status'] == 'lost') {
                    $icon .= "down-red.png";
                } else { $icon .= "down-green.png";}
                break;
            case "outbound":
                if ($call['status'] == 'lost') {
                    $icon .= "up-red.png";
                } else { $icon .= "up-green.png"; }
                break;
            case "internal":
                if ($call['status'] == 'lost') {
                    $icon .= "right-red.png";
                } else { $icon .= "right-green.png"; }
                break;
            default:
                break;
        }
        if ($call['ivrdialed'] == "t") {
            $ivrdialed = "Таймаут";
        } elseif ($call['ivrdialed'] == "s") {
            $ivrdialed = "-";
        } else { $ivrdialed = $call['ivrdialed'];}
        echo '<tr>';
        echo "<td><img src=\"{$icon}\" border=\"0\"></td>";
        echo "<td>{$call['starttime']}</td>";
        echo "<td>{$call['callerid']}</td>";
        echo "<td>{$call['exten']}</td>";
        echo "<td>{$ivrdialed}</td>";
        echo "<td>{$call['answered']}</td>";
        echo "<td>{$call['transferredto']}</td>";
        echo "<td>{$call['fullduration']}</td>";
        echo "<td>{$call['talkduration']}</td>";
        echo "<td>{$call['provider']}</td>";
        echo "<td>{$call['price']}</td>";
        echo "<td>{$call['value']}</td>";
        //echo "<td>{$call['recordfile']}</td>";
        $calldate = substr("{$call['starttime']}", 0, 10);
        $today = date("Y-m-d");
        if ($_SESSION['userid'] == 1) {
            if ($calldate < $today) {
                $record = str_replace('/home/aster/record', 'oldrecord', $call['recordfile']);
                if(file_exists("$record") and ($call['status'] != "lost")) {
                    echo "<td><audio src=\"$record\" controls preload=\"metadata\"></audio></td>";
                } else {echo "<td>Запись отсутствует</td>";}
            } elseif (file_exists("{$call['recordfile']}") and ($call['status'] != "lost")) {
                $record = str_replace('/home/aster/record', 'record', $call['recordfile']);
                echo "<td><audio src=\"$record\" controls preload=\"metadata\"></audio></td>";
            } else {echo "<td>Запись отсутствует</td>";}
        } else {echo "<td>Информация о записи недоступна</td>";}
        echo "<td><a href=\"show_cel.php?linkedid={$call['uniqueid']}\" target=\"_blank\">Смотреть лог</a></td>";
        echo '</tr>';
        $rowsCount++;
    }
}
mysqli_close($my_connection);
?>