<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Gr&aacute;ficos (BETA!)</h2>

<?php if (isset($_POST['submit'])) {
	require_once 'Rserve-php/Connection.php';
			try{
				$r = new Rserve_Connection(RSERVE_HOST);
			} catch (Exception $e) {
				echo 'Erro interno ao conectar no servidor. Notifique os administradores do sistema!<br>';
        if (error_reporting() & E_ERROR)
          echo $e;
			}
			try {
        $text  = 'source("'.$BASEDIR.'/corretor.R");';
        $text .= 'con <- connect("'.$DBUSER.'","'.$DBPASS.'","'.$DBNAME.'");';
        $text .= 'PATH <- "'.$BASEDIR.'";';
        $text .= 'porDow(); porHora();';   
        $x = $r->evalString($text);   
			} catch (Exception $e) {
				echo 'Erro interno ao gerar os graficos.<br>';
        if (error_reporting() & E_ERROR)
          echo $e;
			}
} 
?>

<form action="#" method="post"><button name='submit' type='submit' value='submit'>Atualiza!</button></form>
<p>Atualizados de hora em hora</p>
<p>Gr&aacute;ficos gerais</p>
<br><img src="img/porhora.png">
<br><img src="img/dow.png">
<p>Para a turma de S&atilde;o Paulo 2014</p>
<br><img src="img/exercicio10.png">
<p>Para a turma de Manaus 2013</p>
<br><img src="img/exercicio8.png">
<p>Para a turma de S&atilde;o Paulo 2013</p>
<br><img src="img/exercicio5.png">

</table>
</div>
</body>
</html>
