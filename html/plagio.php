<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Detec&ccedil;&atilde;o de pl&aacute;gio</h2>
<p>Este relat&oacute;rio indica pares de alunos que entregaram exerc&iacute;cios id&ecirc;nticos em diversas ocasi&otilde;es.</p>
<p>Clique no bot&atilde;o abaixo para gerar o relat&oacute;rio.</p>
<form action='?' method='POST'>
<center><p id="Erro">Aten&ccedil;&atilde;o! Como este relat&oacute;rio precisa consultar todos os exerc&iacute;cios que j&aacute; foram entregues, ele pode demorar muito para completar. Evite solicitar este relat&oacute;rio em hor&aacute;rios de muita utiliza&ccedil;&atilde;o do sistema.
<br>	<button type='submit' name='submit' value='turma'>Entendi</button>
</p>
</center>
</form>
<?php 
if (isset($_POST['submit'])) {
?>
<table>
<tr><td>Aluno 1</td><td>Aluno 2</td></td><td>Exerc&iacute;cios iguais</td></tr>

<?php
	$plagio = mysql_query("select x.i1, x.i2, count(*) from (select distinct n1.id_aluno i1, n2.id_aluno i2, n1.id_exercicio from nota n1 join nota n2 on (n1.texto=n2.texto and n1.id_aluno != n2.id_aluno and n1.id_nota != n2.id_nota and n1.id_exercicio = n2.id_exercicio)) x group by x.i1, x.i2 having count(*) >= 5 order by 1");
	$N = mysql_num_rows($plagio)/2;
	for ($i = 0; $i < $N; $i++) {
		$E = mysql_fetch_array($plagio);
		$a1 = new Aluno($E[0]);
		$a2 = new Aluno($E[1]);
		echo "<tr><td>".$a1->getNome()."</td><td>".$a2->getNome()."</td><td>".$E[2]."</td></tr>";
	}

?>
</table>
<?php 
}
?>
</div>
</body>
</html>
