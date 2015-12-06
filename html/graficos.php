<?php require('head.php');
if (! $USER->admin()) {
	echo "<p class='alert alert-danger'>Acesso negado</p>";
	exit;
}
?>
<h2>Gr&aacute;ficos gerais</h2>
<p>Atualizados de hora em hora</p>
<br><img src="img/porhora.png">
<br><img src="img/dow.png">

</div>
</body>
</html>
