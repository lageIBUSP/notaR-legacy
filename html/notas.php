<?php require('head.php');

ini_set('display_errors', 'On');
error_reporting(E_ALL);



if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Relat&oacute;rio de notas</h2>
<p>Escolha a turma: <?php echo SelectTurma(); ?></p>

<table>
<tr><th>Aluno</th>
<?php
$lista_exs = mysql_query("SELECT DISTINCT id_exercicio FROM exercicio JOIN nota USING (id_exercicio) JOIN aluno USING (id_aluno) WHERE id_turma=".$turma->getId()." ORDER BY nome");
$i = 0;
while ($E = mysql_fetch_array($lista_exs)) {
	$ex[$i] = new Exercicio(NULL, $E[0]);
	$n = $ex[$i]->getNome();
	echo "<th>".substr($n,0,strpos($n, " "))."</th>";
	$i++;
}
echo "	</tr>";

foreach (ListAlunos($turma) as $aluno) {
		echo "<tr><td>".$aluno->getNome()."</td>";
	foreach ($ex as $E) {
			echo "<td>".$E->getNota($aluno->getId())."</td>";
	}
		echo "</tr>";
}
?>
</table>
</div>
</body>
</html>
