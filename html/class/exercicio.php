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
		if($aluno) {
			$res = $mysqli->prepare("SELECT max(nota) FROM nota join aluno using (id_aluno) where id_aluno = ? and id_exercicio=?");
			$res->bind_param('ii', $aluno->getId(), $this->id);
			$res->execute();
			$res->bind_result($nota);
			$res->fetch();
			return $nota;
		}
		elseif ($this->user->getLogin()) {
			$res = mysql_query("SELECT max(nota) FROM nota join aluno using (id_aluno) where nome_aluno = '".$this->user->getLogin()."' and id_exercicio=$this->id");
			if (mysql_num_rows($res))
			{
				$res = mysql_fetch_array($res);
				return $res[0];
			}
		}
	}
	public function getPrazo($turma=NULL) {
		global $USER;
		if (!is_null($turma)) {
			$res = mysql_query("SELECT prazo FROM prazo WHERE id_turma = $turma AND id_exercicio=$this->id");
			if (mysql_num_rows($res))
			{
				$res = mysql_fetch_array($res);
				return $res[0];
			}
		}
		elseif ($this->user->getLogin()) {
			$res = mysql_query("SELECT prazo FROM prazo join turma using (id_turma) join aluno using (id_turma) where nome_aluno = '".$this->user->getLogin()."' and id_exercicio=$this->id");
			if (mysql_num_rows($res))
			{
				$res = mysql_fetch_array($res);
				return $res[0];
			}
		}
	}
}
?>
