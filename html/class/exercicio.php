<?php
require_once("config.php");
define("MIN_EX", -2);

class Exercicio {
	private $id, $nome, $html, $precondicoes;
	public function __construct($id=null) {
		global $mysqli;
		global $TURMA;
		if ($id == MIN_EX) {
			$id = null;
			$res = $mysqli->prepare("SELECT DISTINCT id_exercicio FROM exercicio JOIN prazo USING (id_exercicio) WHERE id_turma= ? ORDER BY nome");
			$res->bind_param('i', $TURMA->getId());
			$res->execute();
			if ($res->num_rows) {
				$res->bind_result($id);
				$res->fetch();
			}
		}
		if ($id) {
			$res = $mysqli->prepare("SELECT nome, html, precondicoes FROM exercicio WHERE id_exercicio=?");
			$res->bind_param('i',$id);
			$res->execute();
			$res->bind_result($this->nome, $this->html, $this->precondicoes);
			$res->fetch();
		}
		$this->id = $id;
	}
	public function getProibidos () {
		global $mysqli;
		$res = $mysqli->prepare("SELECT id_proibido FROM proibido WHERE id_exercicio=?");
		$res->bind_param('i', $this->id);
		$res->execute();
		$res->bind_result($new);
		$a = array();
		$ids = array();
		while ($res->fetch()) array_push($ids, $new);
		foreach ($ids as $id) array_push($a, new Proibidos($id));
		return $a;
	}
	public function create($precondicoes, $html, $nome, $testes) {
		global $mysqli;
		$res =$mysqli->prepare("INSERT INTO exercicio (precondicoes, html, nome) 
			VALUES (REPLACE(?, CHAR(13), ''), ?, ?)");
		$res->bind_param('sss', $precondicoes, $html, $nome);
		$res->execute();
		if ($mysqli->error) return "<p class='alert alert-danger'>Erro ao cadastrar o exerc&iacute;cio!</p>";
		$this->id = $mysqli->insert_id; 
		return $this->cadastraTestes($testes, "<p class='alert alert-success'>Exerc&iacute;cio criado.");
	}
	public function cadastraTestes($testes, $msg) { // E impedimentos, por preguiça do programador
		global $mysqli;
		$res = $mysqli->prepare("DELETE FROM teste WHERE id_exercicio=?");
		$res->bind_param('i', $this->id);
		$res->execute();
		$ok = true;
		for ($i=0, $c=0; $i < sizeof($testes[1]); $i++) {
			$j = $i +1;
			if (! empty($testes[1][$i])) {
				$c ++;
				$T = new Teste();
				$ok = $ok AND $T->create($this->id, $j, $testes[0][$i], $testes[1][$i],$testes[2][$i]);
			}
		}
		if (! $ok) $msg .= "</p><p class='alert alert-danger'>Falha ao cadastrar os testes!</p>";
		else $msg .= " com $c testes. </p>";

		$res = $mysqli->prepare("DELETE FROM proibido WHERE id_exercicio=?");
		$res->bind_param('i', $this->id);
		$res->execute();

		$ok = true;
		for ($i=0; $i < sizeof($testes[3]); $i++) {
			if ($testes[3][$i]) {
				$T = new Proibidos();
				$ok = $ok AND $T->create($testes[3][$i], $this->id);
			}
		}
		if (! $ok) $msg .= "<p class='alert alert-danger'>Falha ao cadastrar os impedimentos!</p>";

		$msg .= "Pr&oacute;ximos passos: <ul>
			<li><a href='exercicio.php?exerc=$this->id'>Teste</a> se a corre&ccedil;&atilde;o funciona</li><li><a href='cadastra.php?exerc=$this->id'>Edite</a> as defini&ccedil;&otilde;es deste exerc&iacute;cio</li><li>Determine o <a href='prazos.php'>prazo</a> de entrega</li></ul>";
		return $msg;
	}
	public function altera ($precondicoes, $html, $nome, $testes) {
		global $mysqli;
		$res = $mysqli->prepare("UPDATE exercicio SET 
			precondicoes = REPLACE(?, CHAR(13), ''), html=?, nome=? WHERE id_exercicio=?");
		$res->bind_param('sssi', $precondicoes, $html, $nome, $this->id);
		$res->execute();
		if ($mysqli->error) return "<p class='alert alert-danger'>Erro ao alterar o exerc&iacute;cio!</p>";
		return $this->cadastraTestes($testes, "<p class='alert alert-success'>Exerc&iacute;cio alterado</p>");
	}
	public function getNome() {return $this->nome;}
	public function getHtml() {return $this->html;}
	public function getPrecondicoes() {return $this->precondicoes;}
	public function getId() {return $this->id;}
	public function maxTeste() {
		global $mysqli;
		$res = $mysqli->prepare("SELECT max(ordem) FROM teste WHERE id_exercicio=?");
		$res->bind_param('i',$this->id);
		$res->execute();
		$res->bind_result($max);
		$res->fetch();
		return $max;
	}
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
	public function complecao($turma) {
		global $mysqli;
		$res = $mysqli->prepare("select count(distinct id_aluno) from nota join aluno using(id_aluno) where id_turma=? and id_exercicio = ?");
		$res->bind_param('ii', $turma->getId(), $this->getId());
		$res->execute();
		$res->bind_result($tentativa);
		$res->fetch();
		$res->close();

		$res = $mysqli->prepare("select count(distinct id_aluno) from nota join aluno using(id_aluno) where id_turma=? and id_exercicio = ? and nota=100");
		$res->bind_param('ii', $turma->getId(), $this->getId());
		$res->execute();
		$res->bind_result($cem);
		$res->fetch();
		$res->close();

		$res = $mysqli->prepare("select round(count(id_aluno)/count(distinct id_aluno)) from nota join aluno using(id_aluno) where id_turma=? and id_exercicio=?");
		$res->bind_param('ii', $turma->getId(), $this->getId());
		$res->execute();
		$res->bind_result($diff);
		$res->fetch();
		return array(round(100*$tentativa/$turma->getAlunos()), round(100*$cem/$turma->getAlunos()), $diff);
	}
	public function getPrazo($turma=null) {
		global $USER;
		global $mysqli;
		if ($USER->getId() OR $turma) {
			$res = $mysqli->prepare("SELECT date_format(prazo, '%d/%m/%Y %k:%i') FROM prazo WHERE id_turma = ? AND id_exercicio=?");
			if ($turma) {
				$res->bind_param('ii', $turma->getId(), $this->id);
			} else {
				$res->bind_param('ii', $USER->getTurma(), $this->id);
			}
			$res->execute();
			$res->bind_result($prazo);
			$res->fetch();
			return $prazo;
		}
	}
}

function ListExercicio($turma = null, $reverse = false) {
	global $mysqli;
	if ($reverse) {
		$res = $mysqli->prepare("SELECT DISTINCT id_exercicio FROM exercicio WHERE id_exercicio NOT IN ( SELECT id_exercicio FROM exercicio JOIN prazo USING (id_exercicio) WHERE id_turma = ? ) ORDER BY nome");
		$res->bind_param('i',$turma->getId());
	}
	elseif ($turma) {
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

function SelectExercicio () {
	global $EXERCICIO;
	global $TURMA;
	$T = "<select id='exercicio' name='exercicio' class='form-control'>";
	$T .= "<option value='".MIN_EX."'>---- Exerc&iacute;cios obrigat&oacute;rios ----</option>";
	foreach (ListExercicio($TURMA) as $exercicio) {
		$T .="<option value='".$exercicio->getId()."'";
		if ($EXERCICIO == $exercicio) $T .=" selected ";
		$T .= ">".$exercicio->getNome()."</option>";
	}
	$T .= "<option value='".MIN_EX."'>---- Exerc&iacute;cios opcionais ----</option>";
	foreach (ListExercicio($TURMA, true) as $exercicio) {
		$T .="<option value='".$exercicio->getId()."'";
		if ($EXERCICIO == $exercicio) $T .=" selected ";
		$T .= ">".$exercicio->getNome()."</option>";
	}
	$T .= "</select>";
	return $T;
}
?>
