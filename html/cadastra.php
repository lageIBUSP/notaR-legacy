<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
$id = mysql_real_escape_string($_REQUEST['exerc']);
$X = new Exercicio($user, $id);
?>
<h2>Cadastro de exerc&iacute;cios</h2>
<form name="cadastro" action="#" method="post" enctype="multipart/form-data">
<p>Para a descri&ccedil;&atilde;o dos campos e funcionamento do corretor, leia a documenta&ccedil;&atilde;o.
<br>Nome do exerc&iacute;cio:
<input type="text" name="nome" value="<?php if (isset($_POST['nome'])) echo $_POST['nome']; ?>">
<br>Precondi&ccedil;&otilde;es:
<br><textarea name="precondicoes"><?php if (isset($_POST['precondicoes'])) echo $_POST['precondicoes']; ?></textarea>
<br>HTML:
<br><textarea name="html"><?php if (isset($_POST['html'])) echo $_POST['html']; ?></textarea>
<br>N&uacute;mero de testes: 
<input type="text" name="ntestes" value="<?php if (isset($_POST['ntestes'])) echo $_POST['ntestes']; ?>">
<button type="submit" name="submit" value="alterar">alterar</button>

<h3>Testes</h3>
<table><tr><td><b>Ordem</b></td><td><b>Peso</b></td><td><b>Condi&ccedil;&atilde;o<b></td><td><b>Dica</b></td></tr>
<?php 
for ($i = 0; $i < $_POST['ntestes']; $i ++) {
		echo "<tr>";
		echo "<td><input type='text' name='ordem[]' value='";
		if (isset($_POST['ordem'][$i])) {echo $_POST['ordem'][$i];} else {echo $i;}
		echo "'></td><td><input type='text' name='peso[]' value='";
		if (isset($_POST['peso'][$i])) {echo $_POST['peso'][$i];} else {echo 1;}
		echo "'></td><td><input type='text' name='condicao[]' value=''></td>";
		echo "</tr>";
}
		echo "</table";

?>
<?php 
echo $X->html();
?>

<input type="hidden" name="exerc" value="<?php echo $X->getId(); ?>">
<input type="hidden" name="MAX_FILE_SIZE" value="30000">
<input type="file" name="rfile" id="rfile" accept="text/*">
<button type="submit" name="submit" value="submit">OK</button>
</form>

<a href="index.php">In&iacute;cio</a>

</div>
</body>
</html>
