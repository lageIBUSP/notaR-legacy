<?php require("head.php"); ?>
<br>&nbsp;
<p>Exerc&iacute;cios cadastrados:
<style>
td {border: 2px solid red;}
</style>
<table><th><td>Nota</td><td>Prazo</td></th>
<?php
$res = mysql_query("SELECT id_exercicio FROM exercicio ORDER BY 1 ASC");
while ($exerc = mysql_fetch_array($res)) {
		$X = new Exercicio($user, $exerc[0]);
		echo "<tr><td><a href='exercicio.php?exerc=".$X->getID()."'>".
				$X->nome()."</a></td><td>".$X->nota()."</td><td>".
				$X->prazo()."</td></tr>";
}
?>
</table>
</p>
</div>
</body>
</html>
