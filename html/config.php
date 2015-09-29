<?php
define('RSERVE_HOST','localhost');
$basedir = '/var/www/html/notaR';
error_reporting(0);
ini_set('display_errors', 'On');
error_reporting(E_ERROR);
$mysqli = new mysqli("localhost", "notaR", "notarPw", "notaR");
?>
