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
<h2>Relat&oacute;rio de dificuldades</h2>
<p>Escolha a turma</p>
<form action='dificult.php' method='POST'>
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
<p>M&eacute;dia de tentativas por aluno que entregou cada exerc&iacute;cio:</p>
<table>
<tr><td>Exerc&iacute;cio</td><td>Tentativas</td></tr>

<?php
$lista_exs = mysql_query("select id_exercicio, round(count(id_aluno)/count(distinct id_aluno)) from nota join aluno using(id_aluno) where id_turma=$turma group by id_exercicio order by 2 desc");

while ($E = mysql_fetch_array($lista_exs)) {
	$ex = new Exercicio(NULL, $E[0]);
	echo "<tr><td>".$ex->getNome()."</td><td>".$E[1]."</td></tr>";
}

?>
</table>
</div>
</body>
</html>
