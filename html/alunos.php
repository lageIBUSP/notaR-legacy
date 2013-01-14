<?php require('head.php');
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
###### Codigo aqui
?>
<a href="index.php">In&iacute;cio</a>

</div>
</body>
</html>
