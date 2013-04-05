<?php
require_once("head.php");
### comeca aqui
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
if (isset($_REQUEST['exerc']))
	$X = new Exercicio($_REQUEST['exerc']);
else 
	$X = new Exercicio();

$id = $X->getId();

if (isset($_POST['ntestes'])) {
	$ntestes = $_POST['ntestes'];
} elseif (!empty($id)) {
	$ntestes = $X->maxTeste();
}else {
	$ntestes = 10;
}

?>
<h2>Cadastro de exerc&iacute;cios</h2>
<?php 
if (isset($_POST['submit']) AND $_POST['submit'] == "submit") {
if (empty($id)) 
	echo $X->create($_POST['precondicoes'], $_POST['html'], $_POST['nome'], 
	array($_POST['peso'], $_POST['condicao'], $_POST['dica']));
else
	echo $X->altera($_POST['precondicoes'], $_POST['html'], $_POST['nome'], 
	array($_POST['peso'], $_POST['condicao'], $_POST['dica']));
} else {

echo "<form name=\"cadastro\" action=\"#\" method=\"post\" enctype=\"multipart/form-data\">";
echo "<p>Para a descri&ccedil;&atilde;o dos campos e funcionamento do corretor, leia a <a href='http://www.lage.ib.usp.br/notaR/doku.php?id=cadastro'>ajuda</a>.";
echo "<br>Nome do exerc&iacute;cio:&nbsp;&nbsp;";
echo "<input type=\"text\" name=\"nome\"  style='width: 300px;' value=\"";
if (isset($_POST['nome'])) echo $_POST['nome'];
elseif (!empty($id)) echo $X->getNome();
echo "\">";
echo "<br>Precondi&ccedil;&otilde;es:&nbsp;";
echo "<br><textarea name=\"precondicoes\" rows=7 cols=80>";
if (isset($_POST['precondicoes'])) echo $_POST['precondicoes'];
elseif (!empty($id)) echo $X->getPrecondicoes();
echo "</textarea><br>HTML:<br><textarea name=\"html\" rows=7 cols=80>";
if (isset($_POST['html'])) echo $_POST['html'];
elseif (!empty($id)) echo $X->getHtml();
echo "</textarea><br>N&uacute;mero de testes:&nbsp;&nbsp;";
echo "<input type=\"text\" name=\"ntestes\" value=\"".$ntestes."\">";
echo "<button type=\"submit\" name=\"submit\" value=\"alterar\">alterar</button>";

echo "<h3>Testes</h3>";
echo "<table id='Cadastra'><tr><td><center><b>Ordem</b></center></td><td><center><b>Peso</b></center></td><td><center><b>Condi&ccedil;&atilde;o<center><b></td><td><center><b>Dica</b></center></td></tr>";
for ($i = 0; $i < $ntestes; $i ++) {
	if (!empty($id)) {$T = new Teste($id, $i+1);}
		echo "<tr>";
		echo "<td><center>".($i+1)."</center></td>";
		echo "</td><td><input type='text' name='peso[]' value='";
		if (isset($_POST['peso'][$i])) {echo $_POST['peso'][$i];} 
		elseif (!empty($id) AND $T->peso()) echo $T->peso();
		else {echo 1;}
		echo "'></td><td><input class='long' type='text' name='condicao[]' value='";
		if (isset($_POST['condicao'][$i])) {echo $_POST['condicao'][$i];}
		elseif (!empty($id)) echo $T->condicao();
		echo "'></td><td><input class='long' type='text' name='dica[]' value='";
		if (isset($_POST['dica'][$i])) {echo $_POST['dica'][$i];}
		elseif (!empty($id)) echo $T->dica();
		echo "'></td></tr>";
}
		echo "</table>";


echo "<button type=\"submit\" name=\"submit\" value=\"submit\">OK</button>";
echo "</form>";
}
?>
</div>
</body>
</html>
