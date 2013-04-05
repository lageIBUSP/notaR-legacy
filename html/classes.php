<?php
require_once("config.php");
require_once("class/aluno.php");
require_once("class/nota.php");
require_once("class/user.php");
require_once("class/turma.php");

class Exercicio {
		private $id;
		private $user;
		public function getId() {
			return $this->id;
		}
		public function getNota($aluno=NULL) {
				if(!is_null($aluno)) {
						$res = mysql_query("SELECT max(nota) FROM nota join aluno using (id_aluno) where id_aluno = $aluno and id_exercicio=$this->id");
						if (mysql_num_rows($res))
						{
								$res = mysql_fetch_array($res);
								return $res[0];
						}
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
		public function getNome() {
				$res = mysql_fetch_array(mysql_query("SELECT nome FROM exercicio WHERE id_exercicio=$this->id"));
				return $res[0];
		}
		public function getHtml() {
				$res = mysql_fetch_array(mysql_query("SELECT html FROM exercicio WHERE id_exercicio=$this->id"));
				return $res[0];
		}
		public function getPrecondicoes() {
				$res = mysql_fetch_array(mysql_query("SELECT precondicoes FROM exercicio WHERE id_exercicio=$this->id"));
				return $res[0];
		}
		public function getPrazo($turma=NULL) {
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
		public function __construct($user, $id) {
				$this->user = $user;
				$this->id = $id;
		}
}

class Proibidos {
	private $id_exercicio;
	public function __construct($id) { 
		$this->id_exercicio = $id; 
	}
	public function pass($string) {
		$res = mysql_query("SELECT palavra FROM proibido WHERE id_exercicio =".$this->id_exercicio." OR id_exercicio IS NULL");
		while ($palavra = mysql_fetch_array($res)) {
			if (strpos($string, $palavra[0])   !== FALSE ) return $palavra[0];
		}
		# Se chegou aqui, nada proibido
		return FALSE;
	}
}

require_once("class/teste.php");
function mres($q) {
	if(is_array($q)) 
		foreach($q as $k => $v) 
			$q[$k] = mres($v); //recursive
	elseif(is_string($q))
		$q = mysql_real_escape_string($q);
	return $q;
}
?>

