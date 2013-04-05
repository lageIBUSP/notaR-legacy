<?php require('head.php');
if (! $USER->getId()) {
		echo "Voc&ecirc; precisa estar logado para acessar esta p&aacute;gina.";
		exit;
}
?>

<h2>Altere sua senha</h2>
<?php
###### Codigo aqui
if(isset($_POST['submit']) AND $_POST['submit']=="altera") {
	if(!empty($_POST['senha'])) {
		if ($_POST['senha'] === $_POST['senha2']) {
			$USER->altera($USER->getNome(), $USER->admin(), new Turma($USER->getTurma()), $_POST['senha']);
			echo "<h3>Senha alterada!</h3>";
		}
		else {
			echo "As senhas digitadas n&atilde;o s&atilde;o iguais!";
		}
	}
}
?>
<form action='senha.php' method='POST'>
<table>
<tr><td>Login:</td><td> <?php echo $USER->getNome(); ?></td></tr>
<tr><td>Nova senha:</td><td> <input type='password' name='senha' value=''></td></tr>
<tr><td>Confirme:</td><td> <input type='password' name='senha2' value=''></td></tr>
</table>
<br>
	<button type='submit' name='submit' value='altera'>Alterar</button>
</form>
</div>
</body>
</html>
