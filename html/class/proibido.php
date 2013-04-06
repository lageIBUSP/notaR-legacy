<?php
require_once("config.php");

class Proibidos {
	private $id, $palavra;
	public function __construct($id=null) { 
		global $mysqli;
		if ($id) {
			$res = $mysqli->prepare("SELECT palavra FROM proibido WHERE id_proibido = ?");
			$res->bind_param('i', $id);
			$res->execute();
			$res->bind_result($this->palavra);
			$res->fetch();
			$this->id= $id; 
		}
	}
	public function getPalavra() {return $this->palavra;}
	public function getId() {return $this->id;}
	public function create($palavra, $id) {
		global $mysqli;
		$res = $mysqli->prepare("INSERT INTO proibido (id_exercicio, palavra) VALUES (?, ?)");
		$res->bind_param('is', $id, $palavra);
		$res->execute();
		if (! $mysqli->error) return false;
		return true;
	}
	public function pass($string, $id_ex) {
		global $mysqli;
		$res = $mysqli->prepare("SELECT palavra FROM proibido WHERE id_exercicio =? OR id_exercicio IS NULL");
		$res->bind_param('i', $id_ex);
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
