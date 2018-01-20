<?php
require 'config.php';

//echo "{$_GET['linkedid']} \n";

$query = "SELECT {$db_celtable}.* FROM {$db_celtable} WHERE linkedid = '{$_GET['linkedid']}'";

//echo "{$query} \n";

$rawFlow = mysqli_query($my_connection, $query);
$rawFlowArr = array();
while ($flow_s = mysqli_fetch_assoc($rawFlow)) {
        $rawFlowArr[] = $flow_s;
}
//$dump = var_dump($rawFlowArr);
//echo "{$dump}";
?>
<table width="100%">
  <thead>
    <tr>
      <th>Тип</th>
      <th>Время</th>
      <th>cid Имя</th>
      <th>cid Номер</th>
      <th>cid ANI</th>
      <th>cid RDNIS</th>
      <th>cid DNID</th>
      <th>Экстеншн</th>
      <th>Контекст</th>
      <th>Имя канала</th>
      <th>Функция</th>  
      <th>Данные функции</th>
      <th>UniqueID</th>
      <th>LinkeID</th>
      <th>Доп. данные</th>
      <th>Связанный канал</th>
      <th>Расширенные данные</th>
    </tr>
  </thead>
  <tbody>
<?php
//echo "{$rawFlowArr[0][eventtype]}";
foreach ($rawFlowArr as $row) {
	//$roww = var_dump($row);
	echo '<tr>';
	echo "<td>{$row['eventtype']}</td>";
	echo "<td>{$row['eventtime']}</td>";
	echo "<td>{$row['cid_name']}</td>";
	echo "<td>{$row['cid_num']}</td>";
	echo "<td>{$row['cid_ani']}</td>";
	echo "<td>{$row['cid_rdnis']}</td>";
	echo "<td>{$row['cid_dnid']}</td>";
	echo "<td>{$row['exten']}</td>";
	echo "<td>{$row['context']}</td>";
	echo "<td>{$row['channame']}</td>";
	echo "<td>{$row['appname']}</td>";
	echo "<td width=\"20%\">{$row['appdata']}</td>";
	echo "<td>{$row['uniqueid']}</td>";
	echo "<td>{$row['linkedid']}</td>";
	echo "<td>{$row['userfield']}</td>";
	echo "<td width=\"15%\">{$row['peer']}</td>";
    echo "<td width=\"15%\">{$row['extra']}</td>";
    echo '<tr>';
}
mysqli_close($my_connection);
?>