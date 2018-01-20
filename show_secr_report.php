<?php
require 'config.php';
require 'functions.php';

include 'form_secr_report.php';

$filters_arr = array();
$generate_csv = false;
$csv_file_name = "";
if (!empty($_POST['filter'])) {
	if ($_POST['startdate']) {
		$filters_arr['starttime'] = $_POST['startdate'] . "T0:0";
		if ($_POST['enddate']) {
			$filters_arr['endtime'] = $_POST['enddate'] . "T23:59";
		} else {
			$filters_arr['endtime'] = date('Y-m-d') . "T23:59";
		}
	} else {
		$filters_arr['starttime'] = date('Y-m-d') . "T0:0";
		$filters_arr['endtime'] = date('Y-m-d') . "T23:59";
	}
	$query = "SELECT *, UNIX_TIMESTAMP(starttime) as sttime FROM {$db_statstable} ";
	$condition = "";
	$condition = addFilterCondition($condition,"starttime BETWEEN '{$filters_arr['starttime']}' AND '{$filters_arr['endtime']}' ");
	if ($condition) {$query .= "WHERE {$condition} AND (inqueue = 'reception' OR exten = '0700' OR exten = '0773' OR exten = '0774')";}
	$filtered_calls = mysqli_query($my_connection, $query);
	$filtered_calls_arr = array();
	while ($call_s = mysqli_fetch_assoc($filtered_calls)) {
		$date_key = date('d-m-Y', (int) $call_s['sttime']);
        $filtered_calls_arr[$date_key][] = $call_s;        
    }
    $reportArr = array();
    foreach ($filtered_calls_arr as $key => $value) {
    	//var_dump($value);
    	$ans = 0;
    	$unans = 0;
    	$l_ans = 0;
    	$l_unans = 0;
    	for ($i=0; $i < count($value); $i++) { 
    		if ($value[$i]['status'] == 'answered' && $value[$i]['direction'] == 'inbound') {
    			$ans++;
    		} else { $unans++; }
    		if ($value[$i]['status'] == 'answered' && $value[$i]['direction'] == 'internal') {
    			$l_ans++;
    		} else { $l_unans++; }
    	}
    	$reportArr[$key]['InboundAnswered'] = $ans;
    	$reportArr[$key]['InboundLost'] = $unans;
    	$reportArr[$key]['InternalAnswered'] = $l_ans;
    	$reportArr[$key]['InternalLost'] = $l_unans;
    }
    $csvReportName = date('Y-m-d') . '.csv';
    $csvReportPath = "/tmp/" . $csvReportName;
    $csv = fopen($csvReportPath, "wt");
    fwrite($csv, "\xEF\xBB\xBF");
    $headersArr = array('Дата', 'Входящих отвечено', 'Внутренних отвечено', 'Входящих потеряно', 'Внутренних потеряно');
    fputcsv($csv, $headersArr, ';', '"');
    foreach ($reportArr as $row) {
        //unset($row['id']);
        fputcsv($csv, $row, ";", '"');
    }
      fclose($csv);
    ?>
    <table>
        <tr>
            <td></td>
            <?php echo "<td><a href=\"downloadcsv.php?csv={$csvReportName}\">Нажмите, чтобы скачать CSV</a></td>"; ?>
        </tr>
    </table>
    <table width="100%">
      <thead>
        <tr>
          <th>Дата</th>
          <th>Входящих отвечено</th>
          <th>Внутренних отвечено</th>
          <th>Входящих потеряно</th>
          <th>Внутренних потеряно</th>
        </tr>
      </thead>
      <tbody>
    <?php
    foreach ($reportArr as $key => $value) {
        echo '<tr>';
        echo "<td>{$key}</td>";
        echo "<td>{$value['InboundAnswered']}</td>";
        echo "<td>{$value['InternalAnswered']}</td>";
        echo "<td>{$value['InboundLost']}</td>";
        echo "<td>{$value['InternalLost']}</td>";
        echo '<tr>';
    }
}
mysqli_close($my_connection);
?>