<?php require('head.php');
if (! $USER->admin()) {
	echo "<p class='alert alert-danger'>Acesso negado</p>";
	exit;
}
require('menu.php');
?>
<h2>Administra&ccedil;&atilde;o de impedimentos</h2>
<?php
if (isset($_REQUEST['delete'])) {
		$pr = new Proibidos($_REQUEST['delete']);
		if ($pr->remove())
			echo "<p class='alert alert-success'>Impedimento removido</p>";
		else 
			echo "<p class='alert alert-danger'>Erro ao remover impedimento!</p>";
}
if(isset($_REQUEST['submit'])) {
		$pr = new Proibidos();
		if ($pr->create($_REQUEST['nome'])) 
			echo "<p class='alert alert-success'>Impedimento criado</p>"; 
		else 
			echo "<p class='alert alert-danger'>Erro ao criar impedimento!</p>";
}
?>
<p>Impedimentos globais cadastrados:</p>
<p>(Para detalhes de como impedimentos funcionam, veja <a href="https://github.com/lageIBUSP/notaR/wiki/Gerenciando-arquivos-e-proibi%C3%A7%C3%B5es">aqui</a>).
<table><tr><th colspan=2>Palavra</th></tr>
<?php
foreach (ListProibidos(true) as $pr) {
    echo "<tr><td>";
    if (!$pr->getHard()) 
        echo "<a href='?delete=".$pr->getId()."'><span class='glyphicon glyphicon-remove'></span></a>";
    echo "</td><td>".$pr->getPalavra()."</td></tr>";
}
?>
</table>

<p>Exerc&iacute;cios com impedimentos locais:</p>
<table><tr><th colspan=2>Palavra</th><th>Exerc&iacute;cio</th></tr>
<?php
foreach (ListProibidos(false) as $pr) {
    echo "<tr><td>";
    $EX = new Exercicio($pr->getExercicio());
    echo "<a href='cadastra.php?exerc=".$pr->getExercicio()."'><span class='glyphicon glyphicon-pencil'></span></a>";
    echo "</td><td>".$pr->getPalavra()."</td><td>".$EX->getNome()."</td></tr>";
}
?>
</table>

<form name="cadastro" action="?" method="post">
<p>&nbsp;</p>
<p>Criar novo impedimento global: <input type="text" name="nome" style="width: 300px;"></p>
<p><button type="submit" name="submit" value="submit">ok</button></p>
</form>

</div>
</body>
</html>
