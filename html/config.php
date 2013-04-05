<?php
define('RSERVE_HOST','localhost');
$basedir = '/var/www/rserve';

error_reporting(0);
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);


$CONFIG['dbserver'] = 'localhost';
$CONFIG['dbuser'] = 'notaR';
$CONFIG['dbpass'] = 'notaRpw';
$CONFIG['dbname'] = 'notaR';

mysql_connect($CONFIG['dbserver'], $CONFIG['dbuser'], $CONFIG['dbpass']);
mysql_select_db($CONFIG['dbname']);
mysql_set_charset("UTF-8");

$mysqli = new mysqli("localhost", "notaR", "notaRpw", "notaR");

?>
