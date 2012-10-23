<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
$id = mysql_real_escape_string($_REQUEST['exerc']);

if (isset($_POST['ntestes'])) {$ntestes = $_POST['ntestes'];} else {$ntestes = 10;}

$X = new Exercicio($user, $id);
?>
<h2>Cadastro de exerc&iacute;cios</h2>
<?php 
if (isset($_POST['submit']) AND $_POST['submit'] == "submit") {
$new = mres($_POST);
	$res = mysql_query("INSERT INTO exercicio (precondicoes, html, nome)
		VALUES ('".
		$new['precondicoes']."', '".$new['html']."', '".$new['nome']."')");
	$my_id = mysql_insert_id();
	echo "Exerc&iacute;cio cadastrado ";	
	for ($i=0, $c=0; $i < $ntestes; $i++) {
		if (! empty($new['condicao'][$i])) {
			$c ++;
			$res = mysql_query("INSERT INTO teste (id_exercicio, ordem,
				peso, condicao, dica) VALUES ($my_id,".
				$new['ordem'][$i].",".
				$new['peso'][$i].", '".$new['condicao'][$i]."','".
				$new['dica'][$i]."')");
		}
	}
	echo "com $c testes.";
	echo "Pr&oacute;ximos passos: <ul>
<li><a href='exercicio.php?exerc=$my_id'>Teste</a> se a corre&ccedil;&atilde;o funciona</li><li><a href='cadastra.php?exerc=$my_id'>Edite</a> as defini&ccedil;&otilde;es deste exerc&iacute;cio</li><li>Determine o <a href='prazo.php?exerc=$my_id'>prazo</a> de entrega</li></ul>";
} 
else {

echo "<form name=\"cadastro\" action=\"#\" method=\"post\" enctype=\"multipart/form-data\">";
echo "<p>Para a descri&ccedil;&atilde;o dos campos e funcionamento do corretor, leia a documenta&ccedil;&atilde;o.";
echo "<br>Nome do exerc&iacute;cio:";
echo "<input type=\"text\" name=\"nome\" value=\"";
if (isset($_POST['nome'])) echo $_POST['nome'];
echo "\">";
echo "<br>Precondi&ccedil;&otilde;es:";
echo "<br><textarea name=\"precondicoes\" rows=7 cols=80>";
if (isset($_POST['precondicoes'])) echo $_POST['precondicoes'];
echo "</textarea><br>HTML:<br><textarea name=\"html\" rows=7 cols=80>";
if (isset($_POST['html'])) echo $_POST['html'];
echo "</textarea><br>N&uacute;mero de testes:";
echo "<input type=\"text\" name=\"ntestes\" value=\"".$ntestes."\">";
echo "<button type=\"submit\" name=\"submit\" value=\"alterar\">alterar</button>";

echo "<h3>Testes</h3>";
echo "<table id='Cadastra'><tr><td><center><b>Ordem</b></center></td><td><center><b>Peso</b></center></td><td><center><b>Condi&ccedil;&atilde;o<center><b></td><td><center><b>Dica</b></center></td></tr>";
for ($i = 0; $i < $ntestes; $i ++) {
		echo "<tr>";
		echo "<td><input type='text' name='ordem[]' value='";
		if (isset($_POST['ordem'][$i])) {echo $_POST['ordem'][$i];} else {echo $i+1;}
		echo "'></td><td><input type='text' name='peso[]' value='";
		if (isset($_POST['peso'][$i])) {echo $_POST['peso'][$i];} else {echo 1;}
		echo "'></td><td><input type='text' name='condicao[]' value='";
		if (isset($_POST['condicao'][$i])) {echo $_POST['condicao'][$i];}
		echo "'></td><td><input type='text' name='dica[]' value='";
		if (isset($_POST['dica'][$i])) {echo $_POST['dica'][$i];}
		echo "'></td></tr>";
}
		echo "</table>";


echo "<button type=\"submit\" name=\"submit\" value=\"submit\">OK</button>";
echo "</form>";
}
?>
<a href="index.php">In&iacute;cio</a>

</div>
</body>
</html>
