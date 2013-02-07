<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
if(isset($_REQUEST['id']))
	$id = mysql_real_escape_string($_REQUEST['id']);
else {
	echo "<p>Erro interno!</p></div></body></html>";
	exit;
}
	$aluno = new Aluno($id);
###### Codigo aqui
if(isset($_POST['submit']) AND $_POST['submit']=="altera") {
	$p=mres($_POST);
	$nome=$p['nome'];
	$turma=$p['turma'];
	if(isset($p['admin'])) {$admin=1;} else {$admin=0;}
	mysql_query("UPDATE aluno set nome_aluno='$nome', admin=$admin, id_turma=$turma WHERE id_aluno=$id");
	if(!empty($p['senha'])) {
		$senha=$p['senha'];
		mysql_query("UPDATE aluno set senha=SHA('$senha') WHERE id_aluno=$id");
	}
}
?>
<h2>Cadastro de alunos</h2>
<p>Para alterar detalhes do aluno, edite o formul&aacute;rio abaixo.</p>
<p>Para recadastrar a senha do aluno, digite uma nova senha.</p>
<form action='aluno.php' method='POST'>
<input type='hidden' name='id' value='<?php echo $aluno->getId(); ?>'>
<p>Login: <input type='text' name='nome' value='<?php echo $aluno->getNome(); ?>'>
<br>Senha: <input type='text' name='senha' value=''>
<br>Turma: <select id='turma' name='turma'>
<?php
$lista_turmas = mysql_query("SELECT id_turma FROM turma ORDER BY id_turma ASC");

while ($T = mysql_fetch_array($lista_turmas)) {
	$loop_turma = new Turma($T[0]);
	echo "	<option value=".$loop_turma->getId();
	if($loop_turma->getId() == $aluno->getTurma()) echo " selected";
	echo ">".$loop_turma->getNome()."</option>";
}
?>
	</select>
<br><label><input type="checkbox" name="admin" value="1" 
<?php if ($aluno->admin()) echo "checked"; ?>
/> Admin</label>
<br>
	<button type='submit' name='submit' value='altera'>Alterar</button>

</form>
</div>
</body>
</html>
