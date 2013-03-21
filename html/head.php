<?php require_once('classes.php') ?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta charset="iso-8859-1">
<meta name="description" content="Um sistema para notas automatizadas em cursos que utilizam a linguagem R">
<title>notaR</title>
</head>
<body>
	<div id="Top">
		<div style="float:left">
			<h1><a href="index.php">notaR</a></h1>
			<p>Um sistema para notas automatizadas em cursos que 
			utilizam a linguagem R</p>
		</div>
		<div style="float:right">
			<br>&nbsp;
			<?php echo $user->loginForm(); ?>
		</div>
	</div>
	<div id="MainDiv">
<?php
if(isset($loginerror))  echo $loginerror; 
?>
