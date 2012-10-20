<form name="notaR" action="#" method="post">
<input type="hidden" name="id.exerc" value="<?php echo $exerc; ?>">
<input type="text" name="texto" size=130 value="<?php if (isset($_POST['texto'])) echo $_POST['texto']; ?>">
<!--/textarea-->
<!--textarea rows=8 cols=70 name="texto"-->
<!--?php if (isset($_POST['texto'])) echo $_POST['texto']; ?-->
<!--/textarea-->
<button type="submit" value="Submit">OK</button>
</form>

<div style="border: 2px dashed black; background: #def">
<?php 
if (isset($_POST['texto'])) {

$user =$user->getLogin();
$exerc=$_POST['id_exerc'];
$texto=$_POST['texto'];
$texto = str_replace('\n', '', $texto);

require_once '/var/www/rserve/config.php';
require_once '/var/www/rserve/Connection.php';

   $r = new Rserve_Connection(RSERVE_HOST);
   $x = $r->evalString('source("/var/www/rserve/corretor.R");');
   $x = $r->evalString('notaR("'.$user.'", '.$exerc.', "'.$texto.'")');   
   echo $x;
}
else 
{ echo "<p>Insira sua resposta no campo acima e aperte OK</p>";
}
?>
</div>
<a href="index.php">In&iacute;cio</a>
