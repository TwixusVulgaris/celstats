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
        <legend class="title">Просмотр отчета по секретарю</legend>
        <table border="0" width="100%">
            <tr>
                <th></th>
                <th></th>
                <th>Условия поиска</th>
            </tr>
            <tr>
                <td rowspan="8"><img src="images/logo.png" align="middle"></td>
                <td align="right">
                    <span>Период</span>
                    <!--<input type="radio" name="sort_field" value="date" <?php if (empty($_REQUEST['sort_field']) || $_REQUEST['sort_field'] == "date") {echo 'checked="checked"';}?>>-->
                </td>
                <td>
                    <span>От:
                    <input type="date" name="startdate" value="<?=$_POST["startdate"] ?>">
                    До:
                    <input type="date" name="enddate" value="<?=$_POST['enddate'] ?>"> Если не задана дата - используется сегодняшняя</span>
                </td>
            </tr>
            <tr>
                <td><a href="show_stats.php">Просмотр статистики звонков</a></td>
<!--                <td><span>Сформировать CSV <input type="checkbox" name="need_csv"></span></td> -->
            </tr>
            <tr>
                <td><a href="index.php?logout=1">Выйти</a></td>
                <td>
                    <input type="submit" name="filter" value="Сформировать">                    
                </td>
            </tr>
        </table>
    </fieldset>
</FORM>
