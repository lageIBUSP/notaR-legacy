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
<h2>Relat&oacute;rio de realiza&ccedil;&atilde;o</h2>
<p>Escolha a turma</p>
<form action='complecao.php' method='POST'>
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
<p>Exerc&iacute;cios por porcentagem de realiza&ccedil;&atilde;o:</p>
<table>
<tr><td>Exerc&iacute;cio</td><td>Tentativa</td></td><td>100%</td></tr>

<?php
$n_turma = mysql_fetch_array(mysql_query("select count(*) from aluno where id_turma =$turma"));
$n_turma = $n_turma[0];
$lista_exs = mysql_query("select id_exercicio from exercicio order by nome asc");

while ($E = mysql_fetch_array($lista_exs)) {
	$ex = new Exercicio(NULL, $E[0]);
$tentativa = mysql_fetch_array(mysql_query("select count(distinct id_aluno) from nota join aluno using(id_aluno) where id_turma=$turma and id_exercicio = $E[0]"));
$cem = mysql_fetch_array(mysql_query("select count(distinct id_aluno) from nota join aluno using(id_aluno) where id_turma=$turma and id_exercicio = $E[0] and nota=100"));
	echo "<tr><td>".$ex->getNome()."</td><td>".round(100*$tentativa[0]/$n_turma)."%</td><td>".round(100*$cem[0]/$n_turma)."%</td></tr>";
}

?>
</table>
</div>
</body>
</html>
