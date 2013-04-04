<?php
require_once("config.php");

class Aluno {
	private $id, $nome, $admin, $turma;
	public function __construct($id=null) {
		global $mysqli;
		if($id) {
			$res = $mysqli->prepare("SELECT nome_aluno, admin, id_turma FROM aluno WHERE id_aluno=?");
			$res->bind_param('i',$id);
			$res->execute();
			$res->bind_result($this->nome, $this->admin, $this->turma);
			$res->fetch();
		}
		$this->id=$id;
	}
	public function create($nome, $turma, $senha) {
		if (strlen($nome) < 4) 
			return "O nome $nome &eacute; muito curto. Crie usu&aacute;rios com no m&iacute;nimo 4 caracteres";
		$res = $mysqli->prepare("INSERT INTO aluno (nome_aluno, id_turma, senha) VALUES (?, ?, SHA1(?)");
		$res->bind_param('sis', $nome, $turma->getId(), $senha);
		$res->execute();
		if ($mysqli->error) return "Houve um erro ao inserir o aluno $nome!";
		else return "Aluno $nome inserido com sucesso";
	}
	public function getNome() {return $this->nome;}
	public function getId() { return $this->id; } 
	public function admin() {return $this->admin ? 1 : 0;}
	public function getTurma() {return $this->turma; }//new?
	public function numNotas() {
		global $mysqli;
		$res = $mysqli->prepare("SELECT COUNT(DISTINCT id_exercicio) FROM nota where id_aluno=?");
		$res->bind_param('i', $this->id);
		$res->execute();
		$res->bind_result($notas);
		$res->fetch();
		return $notas;
	}
	public function altera($nome, $admin, $turma, $senha) {
		global $mysqli;
		if ($senha) {
			$res = $mysqli->prepare("UPDATE aluno set nome_aluno=?, admin=?, id_turma=?, senha=SHA1(?) WHERE id_aluno=?");
			$res->bind_param('siisi', $nome, $admin, $turma->getId(), $senha, $this->id);
		} else {
			$res = $mysqli->prepare("UPDATE aluno set nome_aluno=?, admin=?, id_turma=? WHERE id_aluno=?");
			$res->bind_param('siii', $nome, $admin, $turma->getId(), $this->id);
		}
		$res->execute();
		if (! $mysqli->error) return "Altera&ccedil;&otilde;es feitas com sucesso";
		else return "N&atilde;o foi poss&iacute;vel realizar as altera&ccedil;&otilde;es!";
	}
}

function ListAlunos($turma = null) {
	global $mysqli;
	if ($turma) {
		$res = $mysqli->prepare("SELECT id_aluno FROM aluno WHERE id_turma=?");
		$res->bind_param('i', $turma->getId());
	} else
		$res = $mysqli->prepare("SELECT id_aluno FROM aluno");
	$res->execute();
	$res->bind_result($id);
	$ids = array();
	$a = array();
	while ($res->fetch()) array_push($ids, $id);
	foreach ($ids as $id) array_push($a, new Aluno($id));
	return $a;
}

?>
