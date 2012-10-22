<?php require_once('classes.php') ?>
<html><body>
<h1>notaR</h1>
<?php echo $user->loginForm(); ?>
<p>Para ver suas notas e prazos, fa&ccedil;a login pelo form acima</p>
<br>&nbsp;
<p>Exerc&iacute;cios cadastrados:
<style>
td {border: 2px solid red;}
</style>
<table><th><td>Nota</td><td>Prazo</td></th>
<?php
$res = mysql_query("SELECT id_exercicio FROM exercicios ORDER BY 1 ASC");
while ($exerc = mysql_fetch_array($res)) {
		$X = new Exercicio($user, $exerc[0]);
		echo "<tr><td><a href='exercicio.php?exerc=".$X->getID()."'>".
				$X->nome()."</a></td><td>".$X->nota()."</td><td>".
				$X->prazo()."</td></tr>";
?>
</table>
</p>
</body>
</html>
