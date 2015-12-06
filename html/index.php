<?php require("head.php"); 

if ($USER->admin()) require("menu.php");
?>
<br>&nbsp;
<?php if (! $USER->getId()) { ?>
<p> Bem vindo ao notaR!</p>
<p> Este &eacute; um sistema para auxiliar no aprendizado da linguagem <a href="http://r-project.com">R</a>, 
desenvolvido pelo <a href="http://www.ecologia.ib.usp.br/let">Laborat&oacute;rio de Ecologia Te&oacute;rica</a> da
<a href="http://www.usp.br">Universidade de S&atilde;o Paulo</a>.
<p> Se voc&ecirc; est&aacute; matriculado em um curso que est&aacute; utilizando o notaR, seu professor deve
encaminhar um nome de usu&aacute;rio e senha para voc&ecirc; fazer login. N&atilde;o esque&ccedil;a de alterar a senha
ap&oacute;s o primeiro login!</p>
<p> Caso contr&aacute;rio, voc&ecirc; pode seguir a <a href="http://ecologia.ib.usp.br/bie5782/doku.php">apostila do curso</a>
e completar os exerc&iacute;cios abaixo.</p>
<?php
	echo "<table><thead><tr><th>Nome</th></tr></thead><tbody>\n";
	foreach (ListExercicio() as $X) {
		echo "<tr><td>";
		echo "<a href='exercicio.php?exerc=".$X->getId()."'>".$X->getNome()."</a></td> </tr>\n";
	}
	echo "</tbody></table>";
} else { // $USER->getId()
if ($USER->admin()) echo "<a href=\"cadastra.php\"><span class='btn btn-success'><span class='glyphicon glyphicon-plus' style='padding-right:5px;'></span>Cadastrar novo exerc&iacute;cio</span></a><p></p>"; 
	echo "<p>Exerc&iacute;cios obrigat&oacute;rios:</p>";
	$t = new Turma ($USER->getTurma());
	echo "<table><thead><tr><th ";
	if ($USER->admin()) echo "colspan=2";
	echo ">Nome</th><th>Nota</th><th>Prazo</th></tr></thead><tbody>";
	foreach (ListExercicio($t) as $X) {
		echo "<tr><td>";
		if ($USER->admin()) echo "<a href='cadastra.php?exerc=".$X->getId()."'><span class='glyphicon glyphicon-pencil'></span></a></td><td>";
		if ($X->getNota() == 100) echo "<span class='glyphicon glyphicon-ok-circle icon-ok'></span>";
		echo "<a href='exercicio.php?exerc=".$X->getId()."'>".$X->getNome()."</a>";
		echo "</td><td>".$X->getNota()."</td><td>".$X->getPrazo()."</td></tr>";
	}
	echo "</tbody></table>";
	echo "<p>Exerc&iacute;cios opcionais:</p>";
	echo "<table><thead><tr><th ";
	if ($USER->admin()) echo "colspan=2";
	echo ">Nome</th><th>Nota</th></tr></thead><tbody>";
	foreach (ListExercicio($t, true) as $X) {
		echo "<tr><td>";
		if ($USER->admin()) echo "<a href='cadastra.php?exerc=".$X->getId()."'><span class='glyphicon glyphicon-pencil'></span></a></td><td>";
		echo "<a href='exercicio.php?exerc=".$X->getId()."'>".
			$X->getNome()."</a></td><td>".$X->getNota()."</td></tr>";
	}
	echo "</tbody></table>";
} // USER->getID()

?>
</div>
</body>
</html>
