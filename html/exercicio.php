<?php require('head.php');
if(empty($_REQUEST['exerc'])) {echo "<p class='alert alert-danger'>Erro. Se voc&ecirc; usou um link para chegar aqui, notifique o administrador</p>"; exit;}

$X = new Exercicio($_REQUEST['exerc']);

if (empty($X->getNome())) {echo "<p class='alert alert-danger'>Erro. Se voc&ecirc; usou um link para chegar aqui, notifique o administrador</p>"; exit;}

?>
<h2><?php 
echo $X->getNome(); 
if ($USER->admin()) echo "&nbsp;<span class='small'><a href='cadastra.php?exerc=".$X->getId()."'><span class='glyphicon glyphicon-pencil'></span></a></span>";
?></h2>
<?php 
echo $X->getHtml();
?>
<p>&nbsp;</p>

<form name="notaR" action="#" method="post" enctype="multipart/form-data">
  <span class="btn btn-success fileinput-button" id="fakerfile">
        <i class="glyphicon glyphicon-ok"></i>
        <span>Submeter resposta</span>
  </span>
<input type="file" name="rfile" id="rfile" accept=".R,.r" style="display:none;">
<input type="hidden" name="exerc" value="<?php echo $X->getId(); ?>">
<input type="hidden" name="MAX_FILE_SIZE" value="30000">
<button id="submit" type="submit" value="Submit" style="display: none;">Submeter!</button>
<a href="https://github.com/lageIBUSP/notaR/wiki/Submetendo-respostas">ajuda?</a>

</form>
<p>&nbsp;</p>
<div id="corretoR">
<?php 
if (isset($_POST['exerc'])) {
	require_once 'Rserve-php/Connection.php';

	if (empty($_FILES['rfile']["tmp_name"])) { 
		echo "Nenhum arquivo recebido. Verifique se houve algum problema no upload.";
		echo "<br>Poss&iacute;veis causas de erro: <ul><li>Voc&ecirc; esqueceu de fornecer um nome de arquivo?</li>";
		echo "<li>O arquivo &eacute; grande demais? (m&aacute;ximo aceito: 15 mil caracteres)</li>";
		echo "<li>Voc&ecirc; salvou o arquivo usando algum processador de texto, como o Word?</li>";
		echo "</ul>";
	} else {
		$uploadfile = $BASEDIR ."/tmp/".  basename($_FILES['rfile']['tmp_name']);
		move_uploaded_file($_FILES['rfile']['tmp_name'], $uploadfile);

		### Correcao de bug! O R trava se o editor de texto não encerrou
		#   a ultima linha
		system ("echo ' ' >> $uploadfile");

		$conts = file_get_contents($uploadfile);
		$probs = new Proibidos();
		$teste = $probs->pass($conts, $X->getId());
		if ($teste === FALSE) { # Verifica se existe alguma palavra proibida na resposta
			try{
				$r = new Rserve_Connection(RSERVE_HOST);
			} catch (Exception $e) {
				echo 'Erro interno ao conectar no servidor. Notifique os administradores do sistema!<br>';
        if (error_reporting() & E_ERROR)
          echo $e;
			}
			try {
        $text  = 'source("'.$BASEDIR.'/corretor.R");';
        $text .= 'con <- connect("'.$DBUSER.'","'.$DBPASS.'","'.$DBNAME.'");';
        $text .= 'PATH <- "'.$BASEDIR.'";';
        $text .= 'notaR("'.$USER->getNome().'", '.$X->getId().', "'.$uploadfile.'")';   
        $x = $r->evalString($text);   
				if(is_null($x)) echo "<font color='#8c2618'>Aviso: seu c&oacute;digo cont&eacute;m alguma ".
					"funcionalidade do R n&atilde;o suportada pelo notaR. Remova as fun&ccedil;&otilde;es ".
					"gr&aacute;ficas (como <i>plot</i> ou <i>hist</i>) do seu c&oacute;digo e tente novamente.</font>";
				echo $x;
				echo "<p>Seu c&oacute;digo:</p><p class='code'>".nl2br($conts)."</p>";
			} catch (Exception $e) {
				echo 'Erro interno ao executar o corretor. Verifique se as pre-condi&ccedil;&otilde;es executam.';
        if (error_reporting() & E_ERROR)
          echo $e;
			}
		}
		else {echo "<font color='#8c2618'>AVISO! O arquivo enviado n&atilde;o pode conter a(s) palavra(s) \"$teste\". Corrija essa condi&ccedil;&atilde;o e tente novamente.</font>";}
	}
}
else 
{ echo "<p>Escolha o arquivo de resposta usando o bot&atilde;o acima</p>";
}
?>
</div>

</div>
</body>
</html>
