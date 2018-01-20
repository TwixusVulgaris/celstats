<?php

  function addFilterCondition($where, $add, $and = true) {
    if ($where) {
     if ($and) $where .= "AND $add";
      else $where .= "OR $add";
    }
   else $where = $add;
   return $where;
  }

  function generateCsv(array $calls_arr) {
      $csv_name = date('Y-m-d') . '.csv';
      $csv_path = "/tmp/" . $csv_name;
      $csv = fopen($csv_path, "wt");
      fwrite($csv, "\xEF\xBB\xBF");
      $headers_arr = array('Uniqueid', 'Направление', 'Статус', 'Время поступления','Кто звонил','Куда звонил','Кто ответил','Звонок переведён','Файл','Набрано в IVR','Полная длительность','Длительность разговора', 'Провайдер', 'Код', 'Цена минуты', 'Стоимость разговора', 'Очередь входящих');
      fputcsv($csv, $headers_arr, ';', '"');
      foreach ($calls_arr as $call) {
          unset($call['id']);
          fputcsv($csv, $call, ";", '"');
      }
      fclose($csv);
      return $csv_name;
  }

  function find_userid($login, $password) {
      global $my_connection;
      global $db_userstable;
      $query = "SELECT id FROM {$db_userstable} WHERE name = '{$login}' AND pass = '{$password}'";
      $query_result = mysqli_query($my_connection, $query);
      $user = mysqli_fetch_assoc($query_result);
      return $user['id'];
  }

  function print_login_form() {
      print "<form method='POST'>Логин: <input name='login'/><br/>Пароль: <input name='password' type='password'/><br/><input type='submit' value='Войти'/></form>";
  }

  function find_idx(&$arr, $fieldName, $value) {
      foreach ($arr as $idx=>$row) {
          if ($row[$fieldName] == $value) {return $idx;}
      }
      return -1;
}

?>