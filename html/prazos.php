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
$lista_turmas = mysql_query("SELECT id_turma FROM turma");
while ($T = mysql_fetch_array($lista_turmas)) {
	$turma = new Turma($T[0]);
	echo "	<option value=".$turma->getId().">".$turma->getNome()."</option>";
}
?>
	</select>
	<button type='submit'>ok</button>
</form>
<?php
if(isset($_GET['turma'])){
?>
<p>Prazos cadastrados:</p>
<form action='prazos.php' method='get'>
<table>
<tr><td>Exerc&iacute;cio</td><td>Data</td></tr>
<?php
$lista_exs = mysql_query("SELECT id_exercicio FROM exercicio");

while ($E = mysql_fetch_array($lista_exs)) {
	echo "	<tr>";
	$ex = new Exercicio($E[0]);
	echo "		<td>".$ex->getNome()."</td>";
	echo "		<td><input type='text' id='ex".$ex->getId()."' value='".$ex->getNome()."'></td>";
	echo "	</tr>";
}
?>
</table>
<input type='submit'>Atualiza</input>
</form>
<?php
} #fim do if turma
######
?>
</div>
</body>
</html>
