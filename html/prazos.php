<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
###### Codigo aqui

?>
<h2>Administra&ccedil;&atilde;o de prazos</h2>
<p>Escolha a turma</p>
<form action='prazos.php' method='get'>
	<select id='turma' name='turma'>
<?php
$lista_turmas = mysql_query("SELECT id_turma FROM turma ORDER BY id_turma ASC");

if(isset($_GET['turma']))
	$turma = mysql_real_escape_string($_GET['turma']);
else {
		$T = mysql_fetch_array(mysql_query("SELECT MIN(id_turma) FROM turma"));
		$turma = $T[0];
}

while ($T = mysql_fetch_array($lista_turmas)) {
	$loop_turma = new Turma($T[0]);
	echo "	<option value=".$loop_turma->getId();
	if($loop_turma.getId() == $turma) echo " selected";
	echo ">".$loop_turma->getNome()."</option>";
}
?>
	</select>
	<button type='submit'>ok</button>
<!--/form-->
<?php
?>
<p>Prazos cadastrados:</p>
<!--form action='prazos.php' method='get' -->
<table>
<tr><td>Exerc&iacute;cio</td><td>Data</td></tr>
<?php
$lista_exs = mysql_query("SELECT id_exercicio FROM exercicio");

while ($E = mysql_fetch_array($lista_exs)) {
	echo "	<tr>";
	$ex = new Exercicio(NULL, $E[0]);
	echo "		<td>".$ex->getNome()."</td>";
	echo "		<td><input type='text' id='ex".$ex->getId()."' value='".$ex->getPrazo($turma)."'></td>";
	echo "	</tr>";
}
#echo "<input type='hidden' id='turma' name='turma' value='".$turma."' />";
?>
</table>
<button type='submit'>Atualiza</button>
</form>
<?php
######
#//DEBUG
print_r($_GET);
?>
</div>
</body>
</html>
