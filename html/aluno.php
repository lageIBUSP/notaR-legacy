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
?>
<h2>Cadastro de alunos</h2>
<p>Para alterar detalhes do aluno, edite o formul&aacute;rio abaixo.</p>
<p>Para recadastrar a senha do aluno, digite uma nova senha.</p>
<form action='alunos.php' method='POST'>
<input type='hidden' name='id' value='<?php echo $aluno->getId(); ?>'>
<p>Login: <input type='text' name='nome' value='<?php echo $aluno->getNome(); ?>'>
<br>Senha: <input type='text' name='senha' value=''>
<br>Turma: <?php echo SelectTurma($turma); ?>
<br><label><input type="checkbox" name="admin" value="1" 
<?php if ($aluno->admin()) echo "checked"; ?>
/> Admin</label>
<br>
	<button type='submit' name='submit' value='altera'>Alterar</button>

</form>
</div>
</body>
</html>
