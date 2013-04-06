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
<tr><td>Exerc&iacute;cio</td><td>Tentativa</td></td><td>100%</td><td>Tentativas</td></tr>

<?php
$n_turma = $TURMA->getAlunos();
foreach(ListExercicio($TURMA) as $ex) {
	$rel = $ex->complecao($TURMA);
	echo "<tr><td>".$ex->getNome()."</td><td>".$rel[0]."%</td><td>".$rel[1]."%</td><td>".$rel[2]."</td></tr>";
}

?>
</table>

<img src="img/exercicio<?php echo $TURMA->getId(); ?>.png">
</div>
</body>
</html>
