<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
if(isset($_POST['turma']))
	$turma = mysql_real_escape_string($_POST['turma']);
else {
		$T = mysql_fetch_array(mysql_query("SELECT MIN(id_turma) FROM turma"));
		$turma = $T[0];
}
?>
<h2>Relat&oacute;rio de notas</h2>
<p>Escolha a turma</p>
<form action='notas.php' method='POST'>
	<select id='turma' name='turma'>
<?php
$lista_turmas = mysql_query("SELECT id_turma FROM turma ORDER BY id_turma ASC");

while ($T = mysql_fetch_array($lista_turmas)) {
	$loop_turma = new Turma($T[0]);
	echo "	<option value=".$loop_turma->getId();
	if($loop_turma->getId() == $turma) echo " selected";
	echo ">".$loop_turma->getNome()."</option>";
}
?>
	</select>
	<button type='submit' name='submit' value='turma'>ok</button>
</form>
<table>

<?php
$lista_exs = mysql_query("SELECT DISTINCT id_exercicio FROM exercicio JOIN nota USING (id_exercicio) JOIN aluno USING (id_aluno) WHERE id_turma=$turma");

echo "	<tr><td>Aluno</td>";
$i = 0;
while ($E = mysql_fetch_array($lista_exs)) {
	$ex[$i] = new Exercicio(NULL, $E[0]);
	echo "<td>".$ex[$i]->getId()."</td>";
	$i++;
}
echo "	</tr>";

$lista_alunos = mysql_query("SELECT id_aluno FROM aluno WHERE id_turma=$turma ORDER BY nome_aluno ASC");
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
