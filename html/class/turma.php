<?php
require_once("config.php");
define ("MAX_TURMA", 999);
class Turma {
	private $id, $nome, $total;
	public function getId() {return $this->id;}
	public function getNome() {return $this->nome;}
	public function getAlunos() {return $this->total;}
	public function create($nome) {
		global $mysqli;
		$res = $mysqli->prepare("INSERT INTO turma (nome_turma) VALUES (?);");
		$res->bind_param('s',$nome);
		$res->execute();
		if ($mysqli->error) return false;
		return true;
	}
	public function remove() {
		global $mysqli;
		$res = $mysqli->prepare("DELETE FROM turma WHERE id_turma=?");
		$res->bind_param('i', $this->id);
		$res->execute();
		if ($mysqli->error) return false;
		return true;
	}
	public function __construct($id=null) {
		global $mysqli;
		if ($id == MAX_TURMA) {
			$res = $mysqli->query("SELECT MAX(id_turma) mx FROM turma");
			$res = $res->fetch_assoc();
			$id = $res['mx'];
		}
		if ($id) {
			$res = $mysqli->prepare("SELECT nome_turma, count(1) total 
				FROM turma JOIN aluno USING(id_turma) WHERE id_turma= ?");
			$res->bind_param('i', $id);
			$res->execute();
			$res->bind_result($this->nome, $this->total);
			$res->fetch();
		}
		$this->id = $id;
	}
}

function ListTurmas() {
	global $mysqli;
	$res = $mysqli->query("SELECT id_turma FROM turma ORDER BY id_turma ASC");
	$a = array();
	while ($row = $res->fetch_assoc()) 
		array_push($a, new Turma($row['id_turma']));
	return $a;
}

function SelectTurma($turma) {
	$T = "<select id='turma' name='turma'>";
	$turmas = ListTurmas();
	foreach($turmas as $esta) {
		$T.= "<option value=".$esta->getId();
	if($esta == $turma) $T .= " selected";
	$T .= ">".$esta->getNome()."</option>";
	}
	$T .="</select><button type='submit' name='submit' value='turma'>ok</button> <p>Todo: esse botao sera removido em breve!!</p>";
	return $T;
}

if(isset($_POST['turma'])) $turma = new Turma($_POST['turma']);
else $turma = new Turma(MAX_TURMA);
?>

