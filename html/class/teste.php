<?php
require_once("config.php");

class Teste {
	private $id_exercicio, $ordem, $condicao, $peso, $dica;
	public function __construct($id=null, $i=null) {
		global $mysqli;
		if ($id && $i) {
			$res = $mysqli->prepare("SELECT condicao, peso, dica FROM teste WHERE id_exercicio = ? AND ordem = ?");
			$res->bind_param('ii', $id, $i);
			$res->execute();
			$res->bind_result($this->condicao, $this->peso, $this->dica);
			$res->fetch();
		}
		$this->id_exercicio = $id;
		$this->ordem = $i;
	}
	public function condicao() {return $this->condicao;}
	public function peso() {return $this->peso;}
	public function dica() {return $this->dica;}
	public function create($id, $i, $peso, $condicao, $dica) {
		global $mysqli;
		$res = $mysqli->prepare("INSERT INTO teste (id_exercicio, ordem, peso, condicao, dica)
			VALUES (?, ?, ?, ?, ?)");
		$res->bind_param('iiiss', $id, $i, $peso, $condicao, $dica);
		$res->execute();
		if (! $mysqli->error) return false;
		return true;
	}
}
?>
