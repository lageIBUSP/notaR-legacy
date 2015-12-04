<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}

if (isset($_POST['submit']) AND $_POST['submit'] == "atualiza") {
	// TODO: Transformar isso em algo mais OO
	$post = $_POST;
	$ok = true;
	foreach (array_keys($post) AS $key) {
		if (strpos($key, "ld_")) {
			$new = substr($key, 4);
			$ex = substr($key, 6);
			if ($post[$key] != $post[$new]) {
				if($post[$key] =='') { // novo
					$res = $mysqli->prepare("INSERT INTO prazo (id_exercicio, id_turma, prazo) VALUES (?, ?, STR_TO_DATE(?, '%d/%m/%Y %k:%i'))");
					$res->bind_param('iis',$ex, $TURMA->getId(), $post[$new]);
					$ok = $ok AND $res->execute();
				}
				elseif($post[$new] == '') { // removido
					$res = $mysqli->prepare("DELETE FROM prazo WHERE id_exercicio=? AND id_turma=?");
					$res->bind_param('ii',$ex, $TURMA->getId());
					$ok = $ok AND $res->execute();
				}
				else { // atualizar
					$res = $mysqli->prepare("UPDATE prazo SET prazo=STR_TO_DATE(?,'%d/%m/%Y %k:%i') WHERE id_exercicio=? AND id_turma=?");
					$res->bind_param('sii', $post[$new],$ex, $TURMA->getId());
					$ok = $ok AND $res->execute();
				}
			}
		}
	}
	if ($ok) echo "Prazos alterados!";
	else echo "Houveram erros ao alterar os prazos! Confira os valores abaixo!!";
}

?>
<h2>Administra&ccedil;&atilde;o de prazos</h2>
<form action='prazos.php' method='POST' style='width: 600px;'>

<p>Prazos cadastrados para a turma: <?php echo SelectTurma(); ?></p>
<table style='width: 100%'>
<tr><th>Exerc&iacute;cio</th><th>Data</th></tr>
<?php
foreach(ListExercicio() as $ex) {
	echo "<tr><td>".$ex->getNome()."</td><td>";
	echo "<input type='text' id='ex".$ex->getId()."' name='ex".$ex->getId()."' value='".$ex->getPrazo($TURMA)."' class='timepick'>";
	echo "<input type='hidden' name='old_ex".$ex->getId()."' value='".$ex->getPrazo($TURMA)."'>";
	echo "<a href='#' onclick='delprazo(".$ex->getId()."); return false;' style='padding-left: 3px;'><span class='glyphicon glyphicon-remove'></span></a>";
	echo "</td></tr>";
}
?>
</table>
<p>Para cadastrar novos prazos ou alterar os j&aacute; cadastrados, digite a data e hora na caixa de texto correspondente, no formato "DD/MM/YYYY HH:MM".</p>
<p>Os exerc&iacute;cios sem prazo ser&atilde;o considerados OPCIONAIS, e n&atilde;o ser&atilde;o considerados nos relat&oacute;rios</p>
<button type='submit' name='submit' value='atualiza'>Atualiza</button>
</form>
</div>
</body>
</html>
