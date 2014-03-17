<?php
require("head.php");
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}

###### Codigo aqui
if(isset($_POST['submit']) AND $_POST['submit']=="altera") {
	$aluno = new Aluno($_POST['id']);
	if(isset($_POST['admin'])) $admin=1; else $admin=0;
	echo $aluno->altera($_POST['nome'], $admin, $TURMA, $_POST['senha']);
}
if(isset($_POST['submit']) AND $_POST['submit']=="insere") {
	$arr=preg_split("/\r\n|\r|\n/",$_POST['novos']);
	$senha = $_POST['senha'];
	if (empty($senha)) { echo "<p>Voc&ecirc; deve informar uma senha!</p>";}
	else {
		foreach ($arr as $novo) {
			$aluno = new Aluno();
			echo $aluno->create($novo, $TURMA, $senha)."<br>";
		}
	}
}
?>
<h2>Cadastro de alunos</h2>
<p>Escolha a turma</p>
<form action='alunos.php' method='POST'>
<?php echo SelectTurma(); ?>

<p>Alunos cadastrados:</p>
<table><tr><td>Admin</td><td>Login</td><td>Notas</td><td>Editar</td>
<?php
foreach (ListAlunos($TURMA) as $aluno) {
	echo "<tr><td>";
	if ($aluno->admin()) echo "<img src='img/check.png'>"; else echo "&nbsp;";
	echo "</td><td>".$aluno->getNome()."</td><td>".$aluno->numNotas()."</td><td>";
	echo "<a href='aluno.php?id=".$aluno->getId()."'><img src='img/pen.png'></a></td></tr>";
}
?>
</table>
<p>Para cadastrar novos alunos nesta turma, preencha os logins na caixa de texto abaixo, um por linha:</p>
<textarea name="novos" rows=5 cols=70>
</textarea>
<p>Senha: <input type="text" name="senha">
<br><button type='submit' name='submit' value='insere'>Inserir</button></p>

</form>
</div>
</body>
</html>
