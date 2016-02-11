<?php
require_once("head.php");
if (! $USER->admin()) {
	echo "<p class='alert alert-danger'>Acesso negado</p>";
	exit;
}
?>
<h2>Relat&oacute;rio de notas</h2>
<p>Escolha a turma: <?php echo SelectTurma(); ?></p>

<table>
<tr><th>Aluno</th>
<?php
$ex = ListExercicio($TURMA);
foreach ($ex as $exercicio) {
	echo "<th>".substr($exercicio->getNome(),0,strpos($exercicio->getNome(), " "))."</th>";
}
echo "	</tr>";

foreach (ListAlunos($TURMA) as $aluno) {
		echo "<tr><td>".$aluno->getNome()."</td>";
	foreach ($ex as $E) {
			echo "<td>".$E->getNota($aluno)."</td>";
	}
		echo "</tr>";
}
?>
</table>
</div>
</body>
</html>
