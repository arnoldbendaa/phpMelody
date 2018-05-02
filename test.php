<?php
date_default_timezone_set('UTC');
$firstDayOfLastWeek = mktime(0,0,0,date("m"),date("d")-date("w")-7);
$lastDayOfLastWeek = mktime(0,0,0,date("m"),date("d")-date("w")-1);
echo("Last week began on: ".date("d.m.Y",$firstDayOfLastWeek));
echo("<br>");
echo("Last week ended on: ".date("d.m.Y",$lastDayOfLastWeek));
phpinfo();