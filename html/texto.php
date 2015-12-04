<?php
require_once("head.php");
## COMECA AQUI
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}

if (isset($_POST['aluno'])) $aluno = new Aluno($_POST['aluno']);
else $aluno =null;

if (isset($_POST['exercicio'])) $EXERCICIO = new Exercicio($_POST['exercicio']);
else $EXERCICIO = new Exercicio(MIN_EX);

if (isset($_POST['texto'])) $texto = $_POST['texto'];
else $texto = "";
?>
<h2>Busca em texto</h2>
<p>Encontre o texto submetido pelos alunos para cada exerc&iacute;cio.</p>
<form action='texto.php' method='POST' style='width: 500px;'>
<p>Escolha a turma:<?php echo SelectTurma(); ?></p>
<p>Escolha um exerc&iacute;cio: <?php echo SelectExercicio() ; ?></p>
<ul><li>Escolha um aluno para ver todas as tentativas aqui:
<?php echo SelectAluno($aluno); ?></li>
<li>OU digite um texto para procurar nas respostas
<input type="text" name="texto" value="<?php echo $texto; ?>"></li>
</ul>
<button type="submit" name="submit" value="busca">Busca</button>
</form>
<?php if (isset($_POST["submit"])) {
	echo "<table><tr>";
	if ($texto != "") echo"<td>Aluno</td>";
	echo "<td>Data</td></td><td>Nota</td><td>Texto</td></tr>";
	foreach(ListNota($EXERCICIO, $aluno, $texto) as $nota) {
		echo "<tr>";
		if($texto != "") echo "<td>".$nota->getNomeAluno()."</td>";
		echo "<td>".$nota->getData()."</td><td>".$nota->getNota()."</td><td>".$nota->getTexto()."</td></tr>";
	}
	echo "</table>";
}
?>
</div>
</body>
</html>
