<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
###### Codigo aqui
$lista_turmas = mysql_query("SELECT id_turma FROM turma");
?>
<h2>Administra&ccedil;&atilde;o de turmas</h2>
<p>Turmas cadastradas:</p>
<table><tr><td>Nome</td><td>Alunos</td></tr>
<?php
while ($T = mysql_fetch_array($lista_turmas)) {
	$turma = new Turma($T[0]);
	echo "<tr><td>".$turma->getNome()."</td><td>".$turma->getAlunos()."</td></tr>";
}
?>
</table>

</div>
</body>
</html>
