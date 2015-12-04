<?php
require_once("config.php");
require_once("cron.php");
require_once("class/aluno.php");
require_once("class/nota.php");
require_once("class/user.php");
require_once("class/turma.php");
require_once("class/exercicio.php");
require_once("class/proibido.php");
require_once("class/teste.php");
?>
<!DOCTYPE html>
<html>
    <head>
				<meta charset="iso-8859-1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">   
        <meta name="viewport" content="width=device-width, initial-scale=1">
				<title>notaR</title>
				<meta name="description" content="Um sistema para notas automatizadas em cursos que utilizam a linguagem R" />

  <!-- CSS -->
  <!-- jquery ui css -->
				<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.11.4/themes/blitzer/jquery-ui.css" />
  <!-- Bootstrap css -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <!-- local css -->
				<link rel="stylesheet" type="text/css" href="style.css" />

				<script language="javascript" src="java.js"></script>
				<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
				<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
				<script src="jquery-ui-timepicker-addon.js"></script>
				<script src="jquery-jslatex.js"></script>
        <script>
$(document).ready(function() {
	$('.timepick').datetimepicker({
		dateFormat: "dd/mm/yy",
		timeFormat: "HH:mm",
		stepMinute: 10
	});

    $(".latex").latex();
  // for replacing the ugly file selector with a styled bootstrap alternative
  $("#fakerfile").click(function(e) {
    e.preventDefault();
    $("#rfile").trigger("click");
  });
  $("#rfile").change(function (){
    $("#submit").trigger("click");
  });
});
        </script>
  <!-- Bootstrap code  -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

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
			<?php echo $USER->loginForm(); ?>
		</div>
<div style=" width: 100%; height: 1px; clear:both"></div>
	</div>
	<div id="MainDiv">
<?php
if(isset($loginerror))  echo $loginerror; 
?>
			<!--div id='Erro'><h2>Aviso!</h2>
			<p>O notaR est&aacute; passando por uma manuten&ccedil;&atilde;o no momento. 
			Talvez ocorram coisas inesperadas.<br>
			Sugerimos aproveitar o tempo pra tomar uma gelada, ligar prx namoradx, e outras 
			coisas que o cuRso noRmalmente pRo&iacute;be!
			</p></div-->
