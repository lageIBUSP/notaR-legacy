<?php require_once('classes.php') ?>
<html><body>
<h1>notaR</h1>
<?php echo $user->loginForm(); ?>
<p>Para ver suas notas e prazos, fa&ccedil;a login pelo form acima</p>
<br>&nbsp;
<p>Exerc&iacute;cios cadastrados:
<style>
td {border: 2px solid red;}
</style>
<table><th><td>Nota</td><td>Prazo</td></th>
<tr><td><a href="exercicio1.php">Cria&ccedil;&atilde;o de um data frame</a></td><td><?php echo $user->Nota(2); ?></td><td><?php echo $user->Prazo(2); ?></td></tr>
<tr><td><a href="exercicio2.php">Conta de luz</a></td><td><?php echo $user->Nota(4); ?></td><td><?php echo $user->Prazo(4); ?></td></tr>
<tr><td><a href="exercicio3.php">Vari&acirc;ncia na unha</a></td><td><?php echo $user->Nota(5); ?></td><td><?php echo $user->Prazo(5); ?></td></tr>
</table>
</p>
</body>
</html>
