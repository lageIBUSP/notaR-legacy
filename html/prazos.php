<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
if(isset($_POST['turma']))
	$turma = mysql_real_escape_string($_POST['turma']);
else {
		$T = mysql_fetch_array(mysql_query("SELECT MIN(id_turma) FROM turma"));
		$turma = $T[0];
}
if (isset($_POST['submit']) AND $_POST['submit'] == "atualiza") {
		$post = mres($_POST);
		print_r($post);
		foreach (array_keys($post) AS $key) {
				echo "key $key";
				if (strpos($key, "ld_")) {
						$new = substr($key, 4);
						if ($post[$key] != $post[$new]) echo "Mudei $new";
				}
		}

}

?>
<h2>Administra&ccedil;&atilde;o de prazos</h2>
<p>Escolha a turma</p>
<form action='prazos.php' method='POST'>
	<select id='turma' name='turma'>
<?php
$lista_turmas = mysql_query("SELECT id_turma FROM turma ORDER BY id_turma ASC");

while ($T = mysql_fetch_array($lista_turmas)) {
	$loop_turma = new Turma($T[0]);
	echo "	<option value=".$loop_turma->getId();
	if($loop_turma->getId() == $turma) echo " selected";
	echo ">".$loop_turma->getNome()."</option>";
}
?>
	</select>
	<button type='submit' name='submit' value='turma'>ok</button>
<p>Prazos cadastrados:</p>
<table>
<tr><td>Exerc&iacute;cio</td><td>Data</td></tr>
<?php
$lista_exs = mysql_query("SELECT id_exercicio FROM exercicio");

while ($E = mysql_fetch_array($lista_exs)) {
	echo "	<tr>";
	$ex = new Exercicio(NULL, $E[0]);
	echo "		<td>".$ex->getNome()."</td><td>";
	echo "<input type='text' name='ex".$ex->getId()."' value='".$ex->getPrazo($turma)."'>";
	echo "<input type='hidden' name='old_ex".$ex->getId()."' value='".$ex->getPrazo($turma)."'>";
	echo "</td></tr>";
}
?>
</table>
<button type='submit' name='submit' value='atualiza'>Atualiza</button>
</form>
<?php
######
#//DEBUG
print_r($_REQUEST);
?>
</div>
</body>
</html>
