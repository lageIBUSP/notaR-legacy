<?php 
# Checks if dow.png exists and is recent
if(! file_exists("img/dow.png") or filemtime("img/dow.png") < strtotime('1 hour ago')) {
  require_once 'Rserve-php/Connection.php';
  # Quais sao as turmas existentes?
  $res = $mysqli->query("SELECT id_turma FROM turma ORDER BY id_turma ASC");
  $turmas = "";
	while ($row = $res->fetch_assoc()) 
    $turmas .="porExercicio(".$row['id_turma']."); ";
  try{
    $r = new Rserve_Connection(RSERVE_HOST);
  } catch (Exception $e) {
    if (error_reporting() & E_ERROR) {
      echo 'Erro interno ao conectar no servidor. Notifique os administradores do sistema!<br>';
      echo $e;
    }
  }
  try {
    $text  = 'source("'.$BASEDIR.'/corretor.R");';
    $text .= 'con <- connect("'.$DBUSER.'","'.$DBPASS.'","'.$DBNAME.'");';
    $text .= 'PATH <- "'.$BASEDIR.'";';
    $text .= 'porDow(); porHora();';   
    $text .= $turmas;
    $x = $r->evalString($text);   
  } catch (Exception $e) {
    if (error_reporting() & E_ERROR) {
      echo 'Erro interno ao gerar os graficos.<br>';
      echo $e;
    }
  }
} 
# Clears the older answers in tmp/*
if(! file_exists("tmp/clear") or filemtime("tmp/clear") < strtotime('1 day ago')) {
  array_map('unlink', glob("tmp/php*"));
  touch("tmp/clear");
}
?>
