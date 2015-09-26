<?php
require_once("config.php");

class Aluno {
	protected $id, $nome, $admin, $turma;
	public function __construct($id=null) {
		global $mysqli;
		if($id) {
			$res = $mysqli->prepare("SELECT nome_aluno, admin, id_turma FROM aluno WHERE id_aluno=?");
			$res->bind_param('i',$id);
			$res->execute();
			$res->bind_result($this->nome, $this->admin, $this->turma);
			$res->fetch();
			$res->close();
		}
		$this->id=$id;
	}
	public function create($nome, $turma, $senha) {
		global $mysqli;
		if (strlen($nome) < 4) 
			return "O nome $nome &eacute; muito curto. Crie usu&aacute;rios com no m&iacute;nimo 4 caracteres";
		$res = $mysqli->prepare("INSERT INTO aluno (nome_aluno, id_turma, senha) VALUES (?, ?, SHA1(?))");
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
		if (strlen($nome) < 4) 
			return "O nome $nome &eacute; muito curto. Crie usu&aacute;rios com no m&iacute;nimo 4 caracteres";
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
		$res = $mysqli->prepare("SELECT id_aluno FROM aluno WHERE id_turma=? ORDER BY nome_aluno");
		$res->bind_param('i', $turma->getId());
	} else
		$res = $mysqli->prepare("SELECT id_aluno FROM aluno ORDER BY nome_aluno");
	$res->execute();
	$res->bind_result($id);
	$ids = array();
	$a = array();
	while ($res->fetch()) array_push($ids, $id);
	foreach ($ids as $id) array_push($a, new Aluno($id));
	return $a;
}

function SelectAluno($selected) {
	global $TURMA;
	$T = "<select id='aluno' name='aluno'>";
	foreach (ListAlunos($TURMA) as $aluno) {
		$T.= "	<option value=".$aluno->getId();
		if($selected == $aluno) $T.=" selected";
		$T .=">".$aluno->getNome()."</option>";
	}
	$T.="</select>";
	return $T;
}

function RelPlagio () {
	global $mysqli;
	$plagio = $mysqli->prepare("select x.i1, x.i2, count(*) from (select distinct n1.id_aluno i1, n2.id_aluno i2, n1.id_exercicio from nota n1 join nota n2 on (n1.texto=n2.texto and n1.id_aluno != n2.id_aluno and n1.id_nota != n2.id_nota and n1.id_exercicio = n2.id_exercicio)) x group by x.i1, x.i2 having count(*) >= 4 order by 1");
	$plagio->execute();
	$plagio->bind_result($id1, $id2, $count);
  $ids1 = array(); $ids2 = array(); $counts = array();
	while ($plagio->fetch()) { 
		array_push($ids1, $id1);
		array_push($ids2, $id2);
		array_push($counts, $count);
	}

  if(count($ids2) > 0) {
  	$T = "<table><tr><td>Aluno 1</td><td>Aluno 2</td><td>Exerc&iacute;cios iguais</td></tr>";
	  for ($i =0; $i < sizeof($ids2)/2 ; $i++) {
  		$a1 = new Aluno($ids1[$i]);
	  	$a2 = new Aluno($ids2[$i]);
		  $T .= "<tr><td>".$a1->getNome()."</td><td>".$a2->getNome()."</td><td>".$counts[$i]."</td></tr>";
  	}
	  $T .="</table>";
  } else 
    $T = "Nenhum registro de pl&aacute;gio localizado";
	return $T;
}

?>
