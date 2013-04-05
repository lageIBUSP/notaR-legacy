<?php
require_once("head.php");
## COMECA AQUI
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
if (isset($_POST['aluno'])) 
		$selected = new Aluno($_POST['aluno']);
}
if (isset($_POST['exercicio'])) 
	$exercicio = mysql_real_escape_string($_POST['exercicio']);
else {
		$E = mysql_fetch_array(mysql_query("SELECT id_exercicio FROM exercicio where nome_exercicio = (SELECT MIN(nome_exercicio) FROM exercicio)"));
		$exercicio = $E[0];

}
if (isset($_POST['texto'])) $texto = $_POST['texto'];
else $texto = "";
?>
<h2>Busca em texto</h2>
<p>Encontre o texto submetido pelos alunos para cada exerc&iacute;cio.</p>
<form action='texto.php' method='POST'>
<p>Escolha a turma:<?php echo SelectTurma(); ?></p>
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
<ul><li>Escolha um aluno para ver todas as tentativas aqui:
<?php echo SelectAluno($selected); ?></li>
<li>OU digite um texto para procurar nas respostas
<input type="text" name="texto" value="<?php echo $texto; ?>"></li>
</ul>
<button type="submit" name="submit" value="busca">Busca</button>
</form>
<table>
<tr>
<?php if ($texto != "") echo"<td>Aluno</td>";?><td>Data</td></td><td>Nota</td><td>Texto</td></tr>

<?php
if ($texto == "") 
	$lista_exs = mysql_query("SELECT id_nota FROM nota WHERE id_exercicio=$exercicio AND id_aluno=$aluno ORDER BY data ASC");
else 
	$lista_exs = mysql_query("SELECT id_nota FROM nota JOIN aluno USING (id_aluno) WHERE id_exercicio=$exercicio AND texto LIKE '%$texto%' AND  id_turma=".$TURMA->getId()." ORDER BY data ASC");

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
