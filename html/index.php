<?php require("head.php"); ?>
<?php
if ($user->admin()) {
	echo "<div id='Menu'>";
	echo "Administrar:<br><ul><li><a href='turmas.php'>Turmas</a></li>";
	echo "<li><a href='alunos.php'>Alunos</a></li><li><a href='prazos.php'>Prazos</a></li></ul>";
	echo "<br>Relat&oacute;rios:<ul><li><a href='notas.php'>Notas</a></li>";
	echo "<li><a href='dificult.php'>Dificuldades</a></li>";
	echo "<li><a href='complecao.php'>Realiza&ccedil;&atilde;o</a></li>";
	echo "<li><a href='texto.php'>Busca de texto</a></li>";
	echo"</ul></div>";
}
?>
<br>&nbsp;
<p>Exerc&iacute;cios cadastrados:
<?php
echo "<table><tr>";
if ($user->admin()) echo "<td></td>";
echo "<td>Nome</td><td>Nota</td><td>Prazo</td></th>";
$res = mysql_query("SELECT id_exercicio FROM exercicio ORDER BY nome ASC");
while ($exerc = mysql_fetch_array($res)) {
		$X = new Exercicio($user, $exerc[0]);
		echo "<tr><td>";
		if ($user->admin()) echo "<a href='cadastra.php?exerc=".$X->getID()."'><img src='pen.png'></a></td><td>";
		echo "<a href='exercicio.php?exerc=".$X->getID()."'>".
				$X->getNome()."</a></td><td>".$X->getNota()."</td><td>".
				$X->getPrazo()."</td></tr>";
}
?>
</table>
</p>
<?php if ($user->admin()) echo "<p><a href=\"cadastra.php\">Cadastrar novo exerc&iacute;cio</a></p>"; ?>
</div>
</body>
</html>
