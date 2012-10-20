<?php require_once('classes.php') ?>
<html><head><title>Exercicio</title>
<body>
<h1>Exerc&iacute;cios de leitura e manipula&ccedil;&atilde;o de dados</h1>
<h2>Vari&acirc;ncia na unha</h2>
<?php echo $user->loginForm(); ?>

<style>
td {border: 2px solid red;}
</style>
                                                      
<p>A vari&acirc;ncia amostral &eacute; a soma dos quadrados dos desvios em rela&ccedil;&atilde;o &agrave; m&eacute;dia, 
dividida pelo n&uacute;mero de observa&ccedil;&otilde;es menos um:</p>

<img src="var.png">

<ul><li>
Crie um vetor <i>pesos</i> como indicado no tutorial <a href="http://ecologia.ib.usp.br/bie5782/doku.php?id=bie5782:02_tutoriais:tutorial2:start">C&aacute;lculo da M&eacute;dia</a>, 
e calcule sua vari&acirc;ncia sem usar a fun&ccedil;&atilde;o de vari&acirc;ncia do R. 
Sua solu&ccedil;&atilde;o deve incluir a cria&ccedil;&atilde;o de um objeto com os desvios quadr&aacute;ticos, 
chamado <i>pesos.d2</i>, um objeto com a soma desses desvios, chamado <i>pesos.d2s</i> e um objeto com a vari&acirc;ncia, 
chamado <i>pesos.var</i>.</li>
<li>O desvio-padr&atilde;o &eacute; a raiz quadrada da vari&acirc;ncia. 
Calcule-o a partir do objeto <i>pesos.d2</i>, e sem usar a fun&ccedil;&atilde;o de desvio-padr&atilde;o do R.
Sua solu&ccedil;&atilde;o deve incluir um objeto chamado <i>pesos.d</i> com a raiz quadrada dos desvios quadr&aacute;ticos, 
e um objeto chamado <i>pesos.sd</i>, com o valor do desvio-padr&atilde;o.</li>
</ul>
<?php 

$exerc=5; 
require('form.php');

?>
</body>
</html>

