<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
###### Codigo aqui
$post = mres($_REQUEST);
?>
<h2>Administra&ccedil;&atilde;o de turmas</h2>
<?php
if (isset($post['delete'])) {
		$turma = new Turma($post['delete']);
		if ($turma->remove())	echo "<p>Turma removida</p>"; else echo "<p>Erro ao remover turma! Verifique se a turma tem 0 alunos</p>";
}
if(isset($post['submit'])) {
		$turma = new Turma();
		if ($turma->create($post['nome'])) echo "<p>Turma criada</p>"; else echo "<p>Erro ao criar turma!</p>";
}
?>
<p>Turmas cadastradas:</p>
<table><tr><th colspan=2>Nome</th><th>Alunos</th></tr>
<?php
$lista_turmas = mysql_query("SELECT id_turma FROM turma");
while ($T = mysql_fetch_array($lista_turmas)) {
	$turma = new Turma($T[0]);
	echo "<tr><td><a href='?delete=".$turma->getId()."'><img src='x.png'></a></td><td>".$turma->getNome()."</td><td>".$turma->getAlunos()."</td></tr>";
}
?>
</table>

<form name="cadastro" action="#" method="post">
<p>Criar nova turma: <input type="text" name="nome" style="width: 300px;">
<br><button type="submit" name="submit" value="submit">ok</button>
</p>
</form>

</div>
</body>
</html>
