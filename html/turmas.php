<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
require('menu.php');
?>
<h2>Administra&ccedil;&atilde;o de turmas</h2>
<?php
if (isset($_REQUEST['delete'])) {
		$turma = new Turma($_REQUEST['delete']);
		if ($turma->remove())
			echo "<p>Turma removida</p>";
		else 
			echo "<p>Erro ao remover turma! S&oacute; &eacute; poss&iacute;vel remover turmas vazias!</p>";
}
if(isset($_REQUEST['submit'])) {
		$turma = new Turma();
		if ($turma->create($_REQUEST['nome'])) 
			echo "<p>Turma criada</p>"; 
		else 
			echo "<p>Erro ao criar turma!</p>";
}
?>
<p>Turmas cadastradas:</p>
<table><tr><th colspan=2>Nome</th><th>Alunos</th><th>Exerc&iacute;cios</th></tr>
<?php
foreach (ListTurmas() as $turma) {
	echo "<tr><td><a href='?delete=".$turma->getId()."'><span class='glyphicon glyphicon-remove'></span></a></td><td>".$turma->getNome()."</td><td>".$turma->getAlunos()."</td><td>".$turma->getNEx()."</td></tr>";
}
?>
</table>

<form name="cadastro" action="#" method="post">
<p>&nbsp;</p>
<p>Criar nova turma: <input type="text" name="nome" style="width: 300px;"></p>
<p><button type="submit" name="submit" value="submit">ok</button></p>
</form>

</div>
</body>
</html>
