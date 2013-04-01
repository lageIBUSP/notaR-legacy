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
if(isset($_POST['submit']) AND $_POST['submit']=="insere") {
	$arr=preg_split("/\r\n|\r|\n/",$_POST['novos']);
	$arr = mres($arr);
	$senha = mysql_real_escape_string($_POST['senha']);
	if (empty($senha)) { echo "<p>Voc&ecirc; deve informar uma senha!</p>";}
	else {
		$erros ="";
		foreach ($arr as $novo) {
			if (strlen($novo) < 4) {
				$erros .="<br>'$novo' &eacute; muito curto, crie usu&aacute;rios com no m&iacute;nimo 4 caracteres";
			}
			else {
				mysql_query("INSERT INTO aluno (nome_aluno, id_turma, senha) VALUES ('$novo', $turma, SHA('$senha'))"); 
			}
		}
			echo "<p>Alunos cadastrados.</p>";
			if ($erros) echo "<p>Erros:$erros</p>";
	}
}
?>
<h2>Cadastro de alunos</h2>
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
	if ($aluno->admin()) echo "<img src='img/check.png'>";
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
