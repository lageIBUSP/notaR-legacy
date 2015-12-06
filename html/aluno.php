<?php require('head.php');
if (! $USER->admin()) {
	echo "<p class='alert alert-danger'>Acesso negado</p>";
	exit;
}
require('menu.php');
if(! isset($_REQUEST['id'])) {
	echo "<p class='alert alert-danger'>Erro interno!</p></div></body></html>";
	exit;
}
	$aluno = new Aluno($_REQUEST['id']);
	$TURMA = new Turma($aluno->getTurma()); // override o padrao
?>
<h2>Cadastro de alunos</h2>
<p>Para alterar detalhes do aluno, edite o formul&aacute;rio abaixo.</p>
<p>Para recadastrar a senha do aluno, digite uma nova senha.</p>
<form action='alunos.php' method='POST'>
<input type='hidden' name='id' value='<?php echo $aluno->getId(); ?>'>
<p>Login: <input type='text' name='nome' value='<?php echo $aluno->getNome(); ?>'>
<br>Senha: <input type='text' name='senha' value=''>
<br>Turma: <?php echo SelectTurma(false); ?>
<br><label><input type="checkbox" name="admin" value="1" 
<?php if ($aluno->admin()) echo "checked"; ?>
/><span class='glyphicon glyphicon-education' style='padding-left: 5px;'></span> Admin</label>
<br>
	<button type='submit' name='submit' value='altera'>Alterar</button>

</form>
</div>
</body>
</html>
