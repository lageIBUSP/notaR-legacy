<?php
require_once("head.php");
### comeca aqui
if (! $USER->admin()) {
	echo "<p class= 'alert alert-danger'>Acesso negado</p>";
	exit;
}
if (isset($_REQUEST['exerc']))
	$X = new Exercicio($_REQUEST['exerc']);
else 
	$X = new Exercicio();

$id = $X->getId();

if (isset($_POST['ntestes'])) {
	$ntestes = $_POST['ntestes'];
} elseif (!empty($id) && $X->maxTeste()) {
	$ntestes = $X->maxTeste();
}else {
	$ntestes = 10;
}

$imp = $X->getProibidos();
if (isset($_POST['nimp'])) {
	$nimp = $_POST['nimp'];
} elseif (!empty($id) && $imp) {
	$nimp = sizeof($imp);
}else {
	$nimp=0;
}

if (isset($_POST['submit']) AND $_POST['submit'] == "addnimp") {
	$nimp++;
}

# Codigo de reordenacao de testes
$condicao = $_REQUEST['condicao'];
$peso = $_REQUEST['peso'];
$dica = $_REQUEST['dica'];
$TROCA=-2;
if (isset($_REQUEST['down'])) 
  $TROCA = $_REQUEST['down'];
if (isset($_REQUEST['up'])) 
  $TROCA = $_REQUEST['up'] - 1;

if($TROCA >= 0) {
  $tmp = $condicao[$TROCA+1];
  $condicao[$TROCA+1] = $condicao[$TROCA];
  $condicao[$TROCA] = $tmp;
  $tmp = $dica[$TROCA+1];
  $dica[$TROCA+1] = $dica[$TROCA];
  $dica[$TROCA] = $tmp;
  $tmp = $peso[$TROCA+1];
  $peso[$TROCA+1] = $peso[$TROCA];
  $peso[$TROCA] = $tmp;
}

?>
<h2>Cadastro de exerc&iacute;cios</h2>
<?php 
if (isset($_POST['submit']) AND $_POST['submit'] == "submit") {
if (empty($id)) 
	echo $X->create($_POST['precondicoes'], $_POST['html'], $_POST['nome'], 
	array($_POST['peso'], $_POST['condicao'], $_POST['dica'], $_POST['imp']));
else
	echo $X->altera($_POST['precondicoes'], $_POST['html'], $_POST['nome'], 
	array($_POST['peso'], $_POST['condicao'], $_POST['dica'], $_POST['imp']));
} else {

echo "<form name=\"cadastro\" action=\"#\" method=\"post\" enctype=\"multipart/form-data\">";
echo "<p>Para a descri&ccedil;&atilde;o dos campos e funcionamento do corretor, leia a <a href='https://github.com/lageIBUSP/notaR/wiki/Cadastro'>ajuda</a>.";
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
echo "</textarea>";


echo "<h3>Impedimentos</h3>";
echo "<table class='Cadastra'>";
for ($i = 0; $i < $nimp; $i ++) {
		echo "<tr><td><input type='text' class='long' name='imp[]' value='";
		if (isset($_POST['imp'][$i])) {echo htmlspecialchars($_POST['imp'][$i]);} 
		elseif (!empty($id) AND isset($imp[$i])) echo htmlspecialchars($imp[$i]->getPalavra());
		echo "'></td></tr>";
}
echo "</table>";
echo "<input type='hidden' id='nimp' name='nimp' value='$nimp'>";
echo "<button type=\"submit\" name=\"submit\" value=\"addnimp\">+</button>";

echo "<h3>Testes</h3>";
echo "<br>N&uacute;mero de testes:&nbsp;&nbsp;";
echo "<input type=\"text\" name=\"ntestes\" value=\"".$ntestes."\">&nbsp;";
echo "<button type=\"submit\" name=\"submit\" value=\"alterar\">alterar</button>";

echo "<table class='Cadastra'><tr><td><center><b>Ordem</b></center></td><td><center><b>Peso</b></center></td><td><center><b>Condi&ccedil;&atilde;o</b></center></td><td><center><b>Dica</b></center></td></tr>\n";
for ($i = 0; $i < $ntestes; $i ++) {
	if (!empty($id)) {$T = new Teste($id, $i+1);}
		echo "<tr>";
echo "<td>";
if($i > 0) 
  echo "<button class='btn btn-default' type='submit' name='up' value=$i style='width:20px; padding:0px; border:none;'>
  <span class='glyphicon glyphicon-chevron-up'></span></button>";
else 
  echo "<button class='btn btn-default' style='width:20px; padding:0px; border:none;' disabled>&nbsp;</button>";
echo ($i+1);
if($i == $ntestes - 1) 
  echo "<button class='btn btn-default' style='width:20px; padding:0px; border:none;' disabled>&nbsp;</button>";
else 
  echo "<button class='btn btn-default' type='submit' name='down' value=$i style='width:20px; padding:0px; border:none;'>
  <span class='glyphicon glyphicon-chevron-down'></span></button>";
 echo"</td>";
		echo "<td><input type='text' name='peso[]' value='";
		if (isset($peso[$i])) {echo $peso[$i];} 
		elseif (!empty($id) AND $T->peso()) echo $T->peso();
		else {echo 1;}
		echo "'></td><td><input class='long' type='text' name='condicao[]' value=\"";
		if (isset($condicao[$i])) {echo htmlspecialchars($condicao[$i]);}
		elseif (!empty($id)) echo htmlspecialchars($T->condicao());
		echo "\"></td><td><input class='long' type='text' name='dica[]' value=\"";
		if (isset($dica[$i])) {echo htmlspecialchars($dica[$i]);}
		elseif (!empty($id)) echo htmlspecialchars($T->dica());
		echo "\"></td></tr>";
}
		echo "</table>";

echo "<button type=\"submit\" name=\"submit\" value=\"submit\">OK</button>";
echo "</form>";
}
?>
</div>
</body>
</html>
