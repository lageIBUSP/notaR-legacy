<?php
define('RSERVE_HOST','localhost');
$BASEDIR = '/var/www/html/notaR';
$DBUSER = "notaR";
$DBPASS = "notarPw";
$DBNAME = "notaR";

error_reporting(0);
$mysqli = new mysqli("localhost", $DBUSER, $DBPASS, $DBNAME);
?>
