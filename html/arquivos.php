<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Administra&ccedil;&atilde;o de arquivos de dados</h2>
<?php
if (isset($_REQUEST['delete'])) {
  $file = $_REQUEST['delete'];
  if(emUso($file)) {
    echo "<p class='alert alert-danger'>O arquivo parece estar em uso por um ou mais exerc&iacute;cios!</p>";
  } else if(strpos($file, '/') or strpos($file, '\\')) {
    echo "<p class='alert alert-danger'>Caracter inv&aacute;lido no nome do arquivo!</p>";
  } else {
    unlink($BASEDIR."/files/".$file);
    echo "<p class='alert alert-success'>Arquivo removido</p>";
  }
}
if(isset($_REQUEST['submit'])) {
	if (empty($_FILES['rfile']["name"])) { 
		echo "<p class='alert alert-danger'>Nenhum arquivo recebido. Verifique se houve algum problema no upload.</p>";
  } else {
    move_uploaded_file($_FILES['rfile']['tmp_name'], $BASEDIR."/files/".basename($_FILES['rfile']['name']));
    echo "<p class='alert alert-success'>Envio completo</p>";
  }

}
function emUso($file) {
  global $mysqli;
  $file = "%$file%";
  $res = $mysqli->prepare("SELECT count(*) FROM
    (SELECT id_exercicio FROM exercicio WHERE precondicoes LIKE ?
     UNION ALL
     SELECT id_exercicio FROM teste WHERE condicao LIKE ?) AS x");
  $res->bind_param('ss', $file, $file);
  $res->execute();
  $res->bind_result($n_uso);
  echo "Usos: $n_uso";
  return($n_uso > 0);
}
?>
<p>Arquivos cadastrados:</p>
<table><tr><th colspan=2>Nome</th><th>Em uso</th></tr>
<?php
foreach (glob($BASEDIR."/files/*") as $file) {
  $file = basename($file);
  echo "<tr><td><a href='?delete=".$file."'><span class='glyphicon glyphicon-remove'></span></a></td><td>".$file."</td><td align='center'>";
    if(emUso($file)) echo "<span class='glyphicon glyphicon-file'></span>'";
  echo "</td></tr>";
  }
?>
</table>
<p>&nbsp;</p>
<form name="cadastro" action="?" method="post" enctype='multipart/form-data'>
  <span class="btn btn-success fileinput-button" id="fakerfile">
        <i class="glyphicon glyphicon-file"></i>
        <span>Submeter novo arquivo</span>
  </span>
<input type="file" name="rfile" id="rfile" accept=".txt,.csv,.rdata,.rda" style="display:none;">
<input type="hidden" name="MAX_FILE_SIZE" value="30000">
<button name="submit" id="submit" type="submit" value="Submit" style="display: none;">Submeter!</button>
</form>
<p>&nbsp;</p>

</div>
</body>
</html>
