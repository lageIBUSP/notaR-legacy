<?php require('head.php');
if (! $USER->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Gr&aacute;ficos (BETA!)</h2>
<p>Atualizados de hora em hora</p>
<p>Gr&aacute;ficos gerais</p>
<br><img src="img/porhora.png">
<br><img src="img/dow.png">
<p>Para a turma de Manaus 2013</p>
<br><img src="img/exercicio.png">

</table>
</div>
</body>
</html>
