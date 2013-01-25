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
###### Codigo aqui
?>
<h2>Administra&ccedil;&atilde;o de prazos</h2>
<p>Escolha a turma</p>
<form action='alunos.php' method='POST'>
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

<p>Alunos cadastrados:</p>
<table><tr><td>Admin</td><td>Login</td><td>Notas</td><td>Editar</td>
<?php
$lista_alunos = mysql_query("SELECT id_aluno FROM aluno WHERE id_turma=$turma");
while ($A = mysql_fetch_array($lista_alunos)) {
	$aluno = new Aluno($A[0]);
	echo "<tr><td>";
	if ($aluno->admin()) echo "<img src='check.png'>";
	echo "</td><td>".$aluno->getNome()."</td><td>".$aluno->numNotas()."</td><td>";
	echo "<a href='aluno.php?id=".$aluno->getId()."'><img src='pen.png'></a></td></tr>";
}
?>
</table>
<p>Para cadastrar novos alunos nesta turma, preencha os logins na caixa de texto abaixo:</p>
</div>
</body>
</html>
