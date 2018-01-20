<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8">
    <title>Статистика звонков</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" href="icons/favicon.ico" />
</head>
<body>
</body>
<FORM name="filter" action="?" method=post>
    <fieldset>
        <legend class="title">Просмотр записей о совершенных звонках</legend>
        <table border="0" width="100%">
            <tr>
                <th></th>
                <th>Сортировать по</th>
                <th>Условия поиска</th>
            </tr>
            <tr>
                <td rowspan="8"><img src="images/logo.png" align="middle"></td>
                <td align="right">
                    <span>Дата и время звонка</span>
                    <input type="radio" name="sort_field" value="date" <?php if (empty($_REQUEST['sort_field']) || $_REQUEST['sort_field'] == "date") {echo 'checked="checked"';}?>>
                </td>
                <td>
                    <span>От:
                    <input type="date" name="startdate" value="<?=$_POST["startdate"] ?>">
                    <input type="time" name="starttime" value="<?=$_POST['starttime'] ?>">
                    До:
                    <input type="date" name="enddate" value="<?=$_POST['enddate'] ?>">
                    <input type="time" name="endtime" value="<?=$_POST['endtime'] ?>"> Если не задана дата - используется сегодняшняя, время - от: 00:00 до: 23:59</span>
                </td>
            </tr>
            <tr>
                <td align="right">
                    <span>Номер звонившего</span>
                    <input type="radio" name="sort_field" value="cid" <?php if (isset($_REQUEST['sort_field']) && $_REQUEST['sort_field'] == "cid") {echo 'checked="checked"';}?>>
                </td>
                <td>
                    <input type="text" name="callerid"  value="<?=$_POST['callerid']?>">
                    <label for="cbegins">Начинается на</label>
                    <input type="radio" id="cbegins" name="cid_cond" value="begins" <?php if (empty($_REQUEST['cid_cond']) || $_REQUEST['cid_cond'] == "begins") {echo 'checked="checked"';}?>>
                    <label for="cends">Заканчивается на</label>
                    <input type="radio" id="cends" name="cid_cond" value="ends" <?php if (isset($_REQUEST['cid_cond']) && $_REQUEST['cid_cond'] == "ends") {echo 'checked="checked"';}?>>
                    <label for="ccontains">Содержит</label>
                    <input type="radio" id="ccontains" name="cid_cond" value="contains" <?php if (isset($_REQUEST['cid_cond']) && $_REQUEST['cid_cond'] == "contains") {echo 'checked="checked"';}?>>
                    <label for="cequal">Совпадает</label>
                    <input type="radio" id="cequal" name="cid_cond" value="equal" <?php if (isset($_REQUEST['cid_cond']) && $_REQUEST['cid_cond'] == "equal") {echo 'checked="checked"';}?>>
                </td>
            </tr>
            <tr>
                <td align="right">
                    <span>Набранный номер</span>
                    <input type="radio" name="sort_field" value="exten" <?php if (isset($_REQUEST['sort_field']) && $_REQUEST['sort_field'] == "exten") {echo 'checked="checked"';}?>>
                </td>
                <td>
                    <input type="text" name="exten"  value="<?=$_POST['exten']?>">
                    <label for="ebegins">Начинается на</label>
                    <input type="radio" id="ebegins" name="ext_cond" value="begins" <?php if (empty($_REQUEST['ext_cond']) || $_REQUEST['ext_cond'] == "begins") {echo 'checked="checked"';}?>>
                    <label for="eends">Заканчивается на</label>
                    <input type="radio" id="eends" name="ext_cond" value="ends" <?php if (isset($_REQUEST['ext_cond']) && $_REQUEST['ext_cond'] == "ends") {echo 'checked="checked"';}?>>
                    <label for="econtains">Содержит</label>
                    <input type="radio" id="econtains" name="ext_cond" value="contains" <?php if (isset($_REQUEST['ext_cond']) && $_REQUEST['ext_cond'] == "contains") {echo 'checked="checked"';}?>>
                    <label for="eequal">Совпадает</label>
                    <input type="radio" id="eequal" name="ext_cond" value="equal" <?php if (isset($_REQUEST['ext_cond']) && $_REQUEST['ext_cond'] == "equal") {echo 'checked="checked"';}?>>
                </td>
            </tr>
<!--            <tr>
                <td align="right">
                    <span>Набрано в голосовом меню</span>
                    <input type="radio" name="sort_field" value="ivrd" <?php if (isset($_REQUEST['sort_field']) && $_REQUEST['sort_field'] == "ivrd") {echo 'checked="checked"';}?>>
<!--TODO добавить возможность фильтровать по внутреннему номеру. Рабочая версия - список набиравшихся брать из базы и пихать в
<select name = "ivrdialed">-->
<!--                </td>
                <td>
                    <select name = "ivrdialed">
                        <option <?php if (empty($_REQUEST['ivrdialed'])) {echo 'selected="selected"';}?> value = "">Не важно</option>
                        <option <?php if (isset($_REQUEST['ivrdialed']) && $_REQUEST['ivrdialed'] == 1) {echo 'selected="selected"';}?> value = "1">1</option>
                        <option <?php if (isset($_REQUEST['ivrdialed']) && $_REQUEST['ivrdialed'] == 2) {echo 'selected="selected"';}?> value = "2">2</option>
                        <option <?php if (isset($_REQUEST['ivrdialed']) && $_REQUEST['ivrdialed'] == 3) {echo 'selected="selected"';}?> value = "3">3</option>
                        <option <?php if (isset($_REQUEST['ivrdialed']) && $_REQUEST['ivrdialed'] == "t") {echo 'selected="selected"';}?> value = "t">Таймаут</option>
                        <option <?php if (isset($_REQUEST['ivrdialed']) && $_REQUEST['ivrdialed'] == "internal") {echo 'selected="selected"';}?> value = "internal">Внутренний номер</option>
                    </select>
                </td>
            </tr>-->
            <tr>
                <td align="right">
                    <span>Ответил</span>
                    <input type="radio" name="sort_field" value="answered" <?php if (isset($_REQUEST['sort_field']) && $_REQUEST['sort_field'] == "answered") {echo 'checked="checked"';}?>>
                </td>
                <td>
                    <input type="text" name="answered"  value="<?=$_POST['answered']?>">
                    <label for="abegins">Начинается на</label>
                    <input type="radio" id="abegins" name="answ_cond" value="begins" <?php if (empty($_REQUEST['answ_cond']) || $_REQUEST['answ_cond'] == "begins") {echo 'checked="checked"';}?>>
                    <label for="aends">Заканчивается на</label>
                    <input type="radio" id="aends" name="answ_cond" value="ends" <?php if (isset($_REQUEST['answ_cond']) && $_REQUEST['answ_cond'] == "ends") {echo 'checked="checked"';}?>>
                    <label for="acontains">Содержит</label>
                    <input type="radio" id="acontains" name="answ_cond" value="contains" <?php if (isset($_REQUEST['answ_cond']) && $_REQUEST['answ_cond'] == "contains") {echo 'checked="checked"';}?>>
                    <label for="aequal">Совпадает</label>
                    <input type="radio" id="aequal" name="answ_cond" value="equal" <?php if (isset($_REQUEST['answ_cond']) && $_REQUEST['answ_cond'] == "equal") {echo 'checked="checked"';}?>>
                </td>
            </tr>
            <tr>
                <td align="right">
                    <span>Направление звонка</span>
                    <input type="radio" name="sort_field" value="dir" <?php if (isset($_REQUEST['sort_field']) && $_REQUEST['sort_field'] == "dir") {echo 'checked="checked"';}?>>
                </td>
                <td><span>
                    <select name = "direction">
                        <option <?php if (empty($_REQUEST['direction'])) {echo 'selected="selected"';}?> value = "">Любое</option>
                        <option <?php if (isset($_REQUEST['direction']) && $_REQUEST['direction'] == 1) {echo 'selected="selected"';}?> value = "1">Входящие</option>
                        <option <?php if (isset($_REQUEST['direction']) && $_REQUEST['direction'] == 2) {echo 'selected="selected"';}?> value = "2">Исходящие</option>
                        <option <?php if (isset($_REQUEST['direction']) && $_REQUEST['direction'] == 3) {echo 'selected="selected"';}?> value = "3">Внутренние</option>
                    </select>
                       Статус звонка:
                    <select name = "status">
                        <option <?php if (empty($_REQUEST['status'])) {echo 'selected="selected"';}?> value = "">Любой</option>
                        <option <?php if (isset($_REQUEST['status']) && $_REQUEST['status'] == 1) {echo 'selected="selected"';}?> value = "1">Отвеченный</option>
                        <option <?php if (isset($_REQUEST['status']) && $_REQUEST['status'] == 2) {echo 'selected="selected"';}?> value = "2">Неотвеченный</option>
                    </select></span>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <input type="radio" id="sort_asc" name="sort_type" value="asc" <?php if (empty($_REQUEST['sort_type']) || $_REQUEST['sort_type'] == "asc") {echo 'checked="checked"';}?>>
                    <label for="sort_asc">По возрастанию</label>
                    <input type="radio" id="sort_desc" name="sort_type" value="desc" <?php if (isset($_REQUEST['sort_type']) && $_REQUEST['sort_type'] == "desc") {echo 'checked="checked"';}?>>
                    <label for="sort_desc">По убыванию</label>
                </td>
                <td>
                    <span>Выбрать звонков: <input type="text" name="limit" <?php if (empty($_REQUEST['limit'])) {echo 'value="100"';} else {echo "value=\"{$_REQUEST['limit']}\"";}?></span>
                    <span>Сформировать CSV <input type="checkbox" name="need_csv"></span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="filter" value="Искать">
                    <a href="index.php?logout=1">Выйти</a>
                </td>
            </tr>
        </table>
    </fieldset>
</FORM>
