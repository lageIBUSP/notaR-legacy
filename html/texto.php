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
if (isset($_POST['aluno'])) 
	$aluno = mysql_real_escape_string($_POST['aluno']);
else {
		$A = mysql_fetch_array(mysql_query("SELECT id_aluno FROM aluno where nome_aluno = (SELECT MIN(nome_aluno) FROM aluno WHERE id_turma=$turma)"));
		$aluno = $A[0];
}
if (isset($_POST['exercicio'])) 
	$exercicio = mysql_real_escape_string($_POST['exercicio']);
else {
		$E = mysql_fetch_array(mysql_query("SELECT id_exercicio FROM exercicio where nome_exercicio = (SELECT MIN(nome_exercicio) FROM exercicio)"));
		$exercicio = $E[0];

}
if (isset($_POST['texto'])) $texto = mysql_real_escape_string($_POST['texto']);
else $texto = "";
?>
<h2>Busca em texto</h2>
<p>Encontre o texto submetido pelos alunos para cada exerc&iacute;cio.</p>
<form action='texto.php' method='POST'>
<p>Escolha a turma:
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
	<button type='submit' name='submit' value='turma'>ok</button></p>
<p>Escolha um exerc&iacute;cio: 
	<select id='exercicio' name='exercicio'>
<?php
$lista_exercicio = mysql_query("SELECT id_exercicio FROM exercicio ORDER BY nome ASC");

while ($T = mysql_fetch_array($lista_exercicio)) {
	$loop_exercicio = new Exercicio(NULL, $T[0]);
	echo "	<option value=".$loop_exercicio->getId();
	if($loop_exercicio->getId() == $exercicio) echo " selected";
	echo ">".$loop_exercicio->getNome()."</option>";
}

?>
</select></p>
<ul><li>Escolha um aluno para ver todas as tentativas OU</li>
<li>Digite um texto para procurar nas respostas</li></ul>
	<select id='aluno' name='aluno'>
<?php
$lista_alunos = mysql_query("SELECT id_aluno FROM aluno WHERE id_turma=$turma ORDER BY nome_aluno ASC");

while ($T = mysql_fetch_array($lista_alunos)) {
	$loop_aluno = new Aluno($T[0]);
	echo "	<option value=".$loop_aluno->getId();
	if($loop_aluno->getId() == $aluno) echo " selected";
	echo ">".$loop_aluno->getNome()."</option>";
}

?>
</select> OU
<input type="text" name="texto" value="<?php echo $texto; ?>">
<button type="submit" name="submit" value="busca">Busca</button>
</form>
<table>
<tr>
<?php if ($texto != "") echo"<td>Aluno</td>";?><td>Data</td></td><td>Nota</td><td>Texto</td></tr>

<?php
if ($texto == "") 
	$lista_exs = mysql_query("SELECT id_nota FROM nota WHERE id_exercicio=$exercicio AND id_aluno=$aluno ORDER BY data ASC");
else 
	$lista_exs = mysql_query("SELECT id_nota FROM nota WHERE id_exercicio=$exercicio AND texto LIKE '%$texto%' ORDER BY data ASC");

while ($N = mysql_fetch_array($lista_exs)) {
	$ex = new Nota($N[0]);
	echo "<tr>";
	if($texto != "") echo "<td>".$ex->getNomeAluno()."</td>";
	echo "<td>".$ex->getData()."</td><td>".$ex->getNota()."</td><td>".$ex->getTexto()."</td></tr>";
}
?>
</table>
</div>
</body>
</html>
