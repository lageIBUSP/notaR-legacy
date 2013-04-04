<?php
require_once("config.php");

class Nota {
	private $id, $nome, $nota, $data, $texto;
	public function __construct($id) {
		global $mysqli;
		$res = $mysqli->prepare("SELECT nome_aluno, nota, data, texto FROM aluno JOIN nota USING (id_aluno) WHERE id_nota=?");
		$res->bind_param('i',$id);
		$res->execute();
		$res->bind_result($this->nome, $this->nota, $this->data, $this->texto);
		$res->fetch();
		$this->id=$id;
	}
	public function getNomeAluno() {return $this->nome;}
	public function getNota() {return $this->nota;}
	public function getData() {return $this->data;}
	public function getTexto() {return nl2br($this->texto);}
}

?>
