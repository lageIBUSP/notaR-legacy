<?php
require_once("config.php");
define ("MAX_TURMA", -1);
class Turma {
	private $id, $nome, $total;
	public function getId() {return $this->id;}
	public function getNome() {return $this->nome;}
	public function getAlunos() {return $this->total;}
	public function getNEx() {
		global $mysqli;
		$res = $mysqli->prepare("SELECT count(1) total FROM prazo WHERE id_turma= ?");
		$res->bind_param('i', $this->id);
		$res->execute();
		$res->bind_result($n_ex);
		$res->fetch();
		return $n_ex;
	}
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

function SelectTurma($reload = true) { // change the elem id to prevent auto reload
	global $TURMA;
	$T = "<select id='turma' name='turma' ";
	if ($reload) $T.= "onchange=\"window.location='?turma='+this.value;\"";
	$T .=" class='form-control'>";
	$turmas = ListTurmas();
	foreach($turmas as $esta) {
		$T.= "<option value=".$esta->getId();
		if($esta == $TURMA) $T .= " selected";
		$T .= ">".$esta->getNome()."</option>";
	}
	$T .="</select>";
	return $T;
}

if(isset($_REQUEST['turma'])) $TURMA = new Turma($_REQUEST['turma']);
else $TURMA = new Turma(MAX_TURMA);
?>

