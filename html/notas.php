<?php require('head.php');
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

$lista_alunos = mysql_query("SELECT id_aluno FROM aluno WHERE id_turma=".$turma->getId()." ORDER BY nome_aluno ASC");
while ($A = mysql_fetch_array($lista_alunos)) {
		$aluno = new Aluno ($A[0]);
		echo "<tr><td>".$aluno->getNome()."</td>";
	foreach ($ex as $E) {
			echo "<td>".$E->getNota($A[0])."</td>";
	}
		echo "</tr>";
}
?>
</table>
</div>
</body>
</html>
