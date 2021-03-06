<?php
require("head.php");
if (! $USER->admin()) {
	echo "<p class='alert alert-danger'>Acesso negado</p>";
	exit;
}
require('menu.php');

###### Codigo aqui
if(isset($_POST['submit']) AND $_POST['submit']=="altera") {
	$aluno = new Aluno($_POST['id']);
	if(isset($_POST['admin'])) $admin=1; else $admin=0;
	echo $aluno->altera($_POST['nome'], $admin, $TURMA, $_POST['senha']);
}
if(isset($_POST['submit']) AND $_POST['submit']=="insere") {
	$arr=preg_split("/\r\n|\r|\n/",$_POST['novos']);
	$senha = $_POST['senha'];
	if (empty($senha)) { echo "<p class='alert alert-danger'>Voc&ecirc; deve informar uma senha!</p>";}
	else {
		foreach ($arr as $novo) {
			$aluno = new Aluno();
			echo $aluno->create(trim($novo), $TURMA, $senha)."<br>";
		}
	}
}
?>
<h2>Cadastro de alunos</h2>
<form action='alunos.php' method='POST'>
<div class='form-group'>
  <label>Escolha a turma:</label><?php echo SelectTurma(); ?>
</div>

<div class='form-group'>
<label>Alunos cadastrados:</label>
  <table style='width:100%'><tr><th>Admin</th><th>Login</th><th>Notas</th><th>Editar</th>
<?php
foreach (ListAlunos($TURMA) as $aluno) {
	echo "<tr><td align='center'>";
	if ($aluno->admin()) echo "<span class='glyphicon glyphicon-education'></span>"; else echo "&nbsp;";
	echo "</td><td>".$aluno->getNome()."</td><td>".$aluno->numNotas()."</td><td align='center'>";
	echo "<a href='aluno.php?id=".$aluno->getId()."'><span class='glyphicon glyphicon-cog'></span></a></td></tr>";
}
?>
  </table>
</div>
<p>Para cadastrar novos alunos nesta turma, preencha os logins na caixa de texto abaixo, um por linha:</p>
<textarea name="novos" rows=5 class='form-control'> </textarea>
<div class='form-group'>
  <label>Senha:</label> 
  <input type="text" name="senha" class='form-control'>
</div>
<br><button type='submit' name='submit' value='insere'>Inserir</button></p>

</form>
</div>
</body>
</html>
