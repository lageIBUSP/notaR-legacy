<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Gr&aacute;ficos (BETA!)</h2>
<p>Atualizados de hora em hora</p>
<br><img src="img/porhora.png">
<br><img src="img/dow.png">
<br><img src="img/exercicio.png">

</table>
</div>
</body>
</html>
