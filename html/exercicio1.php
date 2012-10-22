<?php require_once('classes.php') ?>
<html><head><title>Exercicio</title>
<body>
<h1>Exerc&iacute;cios de leitura e manipula&ccedil;&atilde;o de dados</h1>
<h2>Cria&ccedil;&atilde;o de um data frame</h2>
<?php echo $user->loginForm(); ?>
<p>Imagine um experimento em que hamsters de dois fen&oacute;tipos
(claros e escuros) recebem tr&ecirc;s tipos diferentes de dieta, e
no qual as diferen&ccedil;as dos pesos (g) entre o fim e o in&iacute;cio 
do experimento sejam:
<style>
td {border: 2px solid red;}
</style>
<table ><th><td>Dieta A</td><td>Dieta B</td><td>Dieta C</td></th>
<tr><td>CLAROS</td><td> 0.1 , 1.1 , 3.7 </td><td> 5.7,   -1.2,   -1.5 </td><td> 3.0,   -0.4,    0.6  </td></tr>
<tr><td>ESCUROS</td><td> 1.5,   -0.1,   2.0  </td><td> 0.6,   -3.0,   -0.3 </td><td> -0.2,    0.3,    1.5 </td></tr>
</table>

<p>Crie um <i>data frame</i> com esses dados, na qual cada hamster seja 
uma linha, e as colunas sejam as vari&aacute;veis cor, dieta e varia&ccedil;&atilde;o
do peso. <b>DICA:</b>   Use as fun&ccedil;&otilde;es de gerar repeti&ccedil;&otilde;es
para criar os vetores dos tratamentos.</p>

<p><b>Importante</b>: o nome do objeto deve ser "hamsters", e o nome das colunas 
deve ser "cor", "dieta" e "pesos", nessa ordem.</p>

<?php 

$exerc=2; 
require('form.php');

?>
</body>
</html>

