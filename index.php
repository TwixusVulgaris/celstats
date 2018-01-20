<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8">
    <title>Статистика звонков</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" href="icons/favicon.ico" />
</head>
<body>
    <?php
        require 'login.php';

        print "<a href=\"show_secr_report.php\">Показать отчет по секретарю</a><br/>";
        echo "<a href=\"show_stats.php\">Показать статистику</a>";
    ?>

</body>
