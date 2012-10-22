<?php require_once('classes.php') ?>
<html><head><title>Exercicio</title>
<body>
<h1>Exerc&iacute;cios de leitura e manipula&ccedil;&atilde;o de dados</h1>
<h2>Conta de luz</h2>
<?php echo $user->loginForm(); ?>

<style>
td {border: 2px solid red;}
</style>
<p>As leituras mensais do medidor de consumo de eletricidade de uma casa foram:</p>
<table><tr><td>
Jan</td><td>Fev</td><td>Mar</td><td>Abr</td><td>Mai</td><td>Jun</td><td>Jul</td><td>Ago</td><td>Set</td><td>Out</td><td>Nov</td><td>Dez</td></tr>
<tr><td>9839</td><td>10149</td><td>10486</td><td>10746</td><td>11264</td><td>11684</td><td>12082</td><td>12599</td><td>13004</td><td>13350</td><td>13717</td><td>14052</td></tr>
</table>

<ul><li>Crie um objeto chamado <i>luz</i> com os valores das leituras, de janeiro a dezembro.</li>
<li>Calcule o consumo de cada m&ecirc;s neste per&iacute;odo com a fun&ccedil;&atilde;o <i>diff</i> e guarde o resultado em um objeto chamado <i>luz.cons</i>.</li>
<li>Calcule a m&eacute;dia, mediana e vari&acirc;ncia dos consumos mensais e guarde-os em objetos chamados <i>luz.m</i>, <i>luz.md</i> e <i>luz.v</i>, respectivamente.</li>
<li>Calcule o m&aacute;ximo e o m&iacute;nimo de consumo mensal e guarde os resultados em um objeto chamado <i>luz.range</i>.
</ul>

<?php 

$exerc=4; 
require('form.php');

?>
</body>
</html>

