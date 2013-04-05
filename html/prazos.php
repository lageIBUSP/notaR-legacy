<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
if (isset($_POST['submit']) AND $_POST['submit'] == "atualiza") {
		$post = mres($_POST);
		foreach (array_keys($post) AS $key) {
				if (strpos($key, "ld_")) {
						$new = substr($key, 4);
						$ex = substr($key, 6);
						if ($post[$key] != $post[$new]) {
								if($post[$key] =='') { // novo
										mysql_query("INSERT INTO prazo (id_exercicio, id_turma->getId(), prazo) VALUES ($ex, $TURMA->getId(), '".$post[$new]."')");
								}
								elseif($post[$new] == '') { // removido
										mysql_query("DELETE FROM prazo WHERE id_exercicio=$ex AND id_turma->getId()=$TURMA->getId()");
								}
								else { // atualizar
										mysql_query("UPDATE prazo SET prazo='".$post[$new]."' WHERE id_exercicio=$ex AND id_turma->getId()=$TURMA->getId()");
								}
						}
				}
		}

}

?>
<h2>Administra&ccedil;&atilde;o de prazos</h2>
<form action='prazos.php' method='POST'>

<p>Prazos cadastrados para a turma: <?php echo SelectTurma(); ?></p>
<table>
<tr><th>Exerc&iacute;cio</th><th>Data</th></tr>
<?php
$lista_exs = mysql_query("SELECT id_exercicio FROM exercicio ORDER by nome");

while ($E = mysql_fetch_array($lista_exs)) {
	echo "	<tr>";
	$ex = new Exercicio(NULL, $E[0]);
	echo "		<td>".$ex->getNome()."</td><td>";
	echo "<input type='text' name='ex".$ex->getId()."' value='".$ex->getPrazo($TURMA->getId())."' style='width: 150px'>";
	echo "<input type='hidden' name='old_ex".$ex->getId()."' value='".$ex->getPrazo($TURMA->getId())."'>";
	echo "</td></tr>";
}
?>
</table>
<p>Para cadastrar novos prazos ou alterar os j&aacute; cadastrados, digite a data e hora na caixa de texto correspondente, no formato "YYYY-MM-DD HH:MM:SS".</p>
<button type='submit' name='submit' value='atualiza'>Atualiza</button>
</form>
</div>
</body>
</html>
