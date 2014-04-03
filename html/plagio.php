<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Detec&ccedil;&atilde;o de pl&aacute;gio</h2>
<p>Este relat&oacute;rio indica pares de alunos que entregaram exerc&iacute;cios id&ecirc;nticos em diversas ocasi&otilde;es.</p>
<p>Clique no bot&atilde;o abaixo para gerar o relat&oacute;rio.</p>
<form action='?' method='POST'>
<center><p id="Erro">Aten&ccedil;&atilde;o! Como este relat&oacute;rio precisa consultar todos os exerc&iacute;cios que j&aacute; foram entregues, ele pode demorar muito para completar. Evite solicitar este relat&oacute;rio em hor&aacute;rios de muita utiliza&ccedil;&atilde;o do sistema.
<br>	<button type='submit' name='submit' value='turma'>Entendi</button>
</p>
</center>
</form>
<?php 
if (isset($_POST['submit'])) {
	echo RelPlagio(); 
}
?>
</div>
</body>
</html>
