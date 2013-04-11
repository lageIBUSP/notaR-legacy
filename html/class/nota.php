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

function listNota($exercicio, $aluno, $texto) { // deve ser invocado com aluno OU com texto
	global $mysqli;
	global $TURMA;
	if ($texto == "") {
		$res = $mysqli->prepare("SELECT id_nota FROM nota WHERE id_exercicio=? AND id_aluno = ? ORDER BY data ASC");
		$res->bind_param('ii', $exercicio->getId(), $aluno->getId());
	} else {
		$texto = '%'.$texto.'%';
		$res = $mysql->prepare("SELECT id_nota FROM nota JOIN aluno USING (id_aluno) WHERE id_exercicio=? AND texto LIKE ? AND  id_turma=? ORDER BY data ASC");
		$res->bind_param('isi', $exercicio->getId(), $texto, $TURMA->getId());
	}
	$res->execute();
	$res->bind_result($id);
	$ids = array();
	$a = array();
	while ($res->fetch()) array_push($ids, $id);
	foreach ($ids as $id) array_push($a, new Nota($id));
	return $a;
}
?>
