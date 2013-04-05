<?php
require_once("config.php");

class Proibidos {
	private $id_exercicio;
	public function __construct($id) { 
		$this->id_exercicio = $id; 
	}
	public function pass($string) {
		global $mysqli;
		$res = $mysqli->prepare("SELECT palavra FROM proibido WHERE id_exercicio =? OR id_exercicio IS NULL");
		$res->bind_param('i', $this->id);
		$res->execute();
		$res->bind_result($palavra);
		while ($res->fetch()) {
			if (strpos($string, $palavra) !== FALSE ) return $palavra;
		}
		# Se chegou aqui, nada proibido
		return FALSE;
	}
}

?>
