<?php
$system_tmp_dir = '/tmp';
$file = ($_REQUEST['csv']);
header ("Content-Type: application/octet-stream");
header ("Accept-Ranges: bytes");
header ("Content-Length: ".filesize("$system_tmp_dir/{$file}"));
header ("Content-Disposition: attachment; filename=".$file);
readfile("$system_tmp_dir/{$file}");
?>