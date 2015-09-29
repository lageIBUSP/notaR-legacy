<?php
define('RSERVE_HOST','localhost');
$BASEDIR = '/var/www/html/notaR';
$DBUSER = "notaR";
$DBPASS = "notarPw";
$DBNAME = "notaR";

error_reporting(0);
ini_set('display_errors', 'On');
error_reporting(E_ERROR);
$mysqli = new mysqli("localhost", $DBUSER, $DBPASS, $DBNAME);
?>
