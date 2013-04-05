<?php
require_once("config.php");
class Exercicio {
	private $id, $nome, $html, $precondicoes;
	public function __construct($id=null) {
		global $mysqli;
		if ($id) {
			$res = $mysqli->prepare("SELECT nome, html, precondicoes FROM exercicio WHERE id_exercicio=?");
			$res->bind_param('i',$id);
			$res->execute();
			$res->bind_result($this->nome, $this->html, $this->precondicoes);
			$res->fetch();
		}
		$this->id = $id;
	}
	public function getNome() {return $this->nome;}
	public function getHtml() {return $this->html;}
	public function getPrecondicoes() {return $this->precondicoes;}
	public function getId() {return $this->id;}
	public function getNota($aluno=null) {
		global $USER;
		global $mysqli;
		if ($aluno OR $USER->getId()) {
			$res = $mysqli->prepare("SELECT max(nota) FROM nota join aluno using (id_aluno) where id_aluno = ? and id_exercicio=?");
			if($aluno) {
				$res->bind_param('ii', $aluno->getId(), $this->id);
			} else {
				$res->bind_param('ii', $USER->getId(), $this->id);
			}
			$res->execute();
			$res->bind_result($nota);
			$res->fetch();
			return $nota;
		}
	}
	public function getPrazo($turma=null) {
		global $USER;
		global $mysqli;
		if ($USER->getId() OR $turma) {
			$res = $mysqli->prepare("SELECT prazo FROM prazo WHERE id_turma = ? AND id_exercicio=?");
			if ($turma) {
				$res->bind_param('ii', $turma->getId(), $this->id);
			} else {
				$res->bind_param('ii', $USER->getTurma(), $this-id);
			}
			$res->execute();
			$res->bind_result($prazo);
			$res->fetch();
			return $prazo;
		}
	}
}

function ListExercicio($turma = null) {
	global $mysqli;
	if ($turma) {
		$res = $mysqli->prepare("SELECT DISTINCT id_exercicio FROM exercicio JOIN prazo USING (id_exercicio) WHERE id_turma= ? ORDER BY nome");
		$res->bind_param('i',$turma->getId());
	} else
		$res = $mysqli->prepare("SELECT id_exercicio FROM exercicio ORDER BY nome");
	$res->execute();
	$res->bind_result($id);
	$ids = array();
	$a = array();
	while ($res->fetch()) array_push($ids, $id);
	foreach ($ids as $id) array_push($a, new Exercicio($id));
	return $a;
}

?>
