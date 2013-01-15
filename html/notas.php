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
?>
<h2>Relat&oacute;rio de notas</h2>
<p>Escolha a turma</p>
<form action='notas.php' method='POST'>
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
</form>
<table>

<?php
//<tr><td>Exerc&iacute;cio</td><td>Data</td></tr>
$lista_exs = mysql_query("SELECT id_exercicio FROM exercicio JOIN nota USING (id_exercicio) JOIN aluno USING (id_aluno) WHERE id_turma=$turma");

$i = 0;


while ($E = mysql_fetch_array($lista_exs)) {
	echo "	<tr>";
	$ex = new Exercicio(NULL, $E[0]);
	echo $ex->getNome();
//	echo "		<td>".$ex->getNome()."</td>";
//	echo "		<td><input type='text' name='ex".$ex->getId()."' value='".$ex->getPrazo($turma)."'></td>";
//	echo "	</tr>";
}
?>
</table>
</div>
</body>
</html>
