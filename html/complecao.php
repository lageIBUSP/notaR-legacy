<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Relat&oacute;rio de realiza&ccedil;&atilde;o</h2>
<p>Escolha a turma: <?php echo SelectTurma(); ?></p>
<p>Exerc&iacute;cios por porcentagem de realiza&ccedil;&atilde;o:</p>
<table>
<tr><td>Exerc&iacute;cio</td><td>Tentativa</td></td><td>100%</td></tr>

<?php
$n_turma = $TURMA->getAlunos();
foreach(ListExercicio($TURMA) as $ex) {
$tentativa = mysql_fetch_array(mysql_query("select count(distinct id_aluno) from nota join aluno using(id_aluno) where id_turma=".$TURMA->getId()." and id_exercicio = ".$ex->getId()));
$cem = mysql_fetch_array(mysql_query("select count(distinct id_aluno) from nota join aluno using(id_aluno) where id_turma=".$TURMA->getId()." and id_exercicio = ".$ex->getId()." and nota=100"));
	echo "<tr><td>".$ex->getNome()."</td><td>".round(100*$tentativa[0]/$n_turma)."%</td><td>".round(100*$cem[0]/$n_turma)."%</td></tr>";
}

?>
</table>
</div>
</body>
</html>
