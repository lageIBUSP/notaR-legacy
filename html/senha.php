<?php require('head.php');

if (empty($user->getLogin())) {
		echo "Voc&ecirc; precisa estar logado para acessar esta p&aacute;gina.";
		exit;
}

###### Codigo aqui
if(isset($_POST['submit']) AND $_POST['submit']=="altera") {
	$id = $user->getId();
	$p=mres($_POST);
	if(!empty($p['senha'])) {
		$senha=$p['senha'];
		mysql_query("UPDATE aluno set senha=SHA('$senha') WHERE id_aluno=$id");
		echo "<h3>Senha alterada!</h3>"
	}
}
?>
<h2>Altere sua senha</h2>
<form action='senha.php' method='POST'>
<p>Login: <?php echo $aluno->getNome(); ?>
<br>Senha: <input type='text' name='senha' value=''>
<br>
	<button type='submit' name='submit' value='altera'>Alterar</button>
</form>
</div>
</body>
</html>
