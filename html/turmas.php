<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
###### Codigo aqui
$lista_turmas = mysql_query("SELECT id_turma FROM turma");
$post = mres($_REQUEST);
?>
<h2>Administra&ccedil;&atilde;o de turmas</h2>
<p>Turmas cadastradas:</p>
<table><tr><td></td><td>Nome</td><td>Alunos</td></tr>
<?php
while ($T = mysql_fetch_array($lista_turmas)) {
	$turma = new Turma($T[0]);
	echo "<tr><td><a href='?delete=".$turma->getId()."'>X</a></td><td>".$turma->getNome()."</td><td>".$turma->getAlunos()."</td></tr>";
}
?>
</table>

<?php
if (isset($post['delete'])) {
		$turma = new Turma($post['delete']);
		if ($turma->remove())	echo "<p>Turma removida</p>"; else echo "<p>Erro ao remover turma! Verifique se a turma tem 0 alunos</p>";
}
?>
</div>
</body>
</html>
