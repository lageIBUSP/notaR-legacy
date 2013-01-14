<?php
require_once("config.php");
class User {
		private $login;
		public function getLogin() {
			if (isset($this->login)) return $this->login;
			return "xuxa";
		}
		public function admin () {
			$res = mysql_fetch_array(mysql_query("SELECT admin FROM aluno where nome_aluno='".$this->login."'"));
			return $res[0];
		}
		public function __construct() {
				if (!isset($_SESSION)) session_start();
				// Para fazer logout:
				if (isset($_GET['logout'])) {
						unset($_SESSION['user']);
						header("Location: index.php");
						exit;
				}
				// Se estamos solicitando login:
				if (isset($_POST['action']) && $_POST['action'] == "login") {
						$nome = mysql_real_escape_string(trim($_POST['login']));
						$senha = sha1(trim($_POST['senha']));
						$res = mysql_query("SELECT * FROM aluno WHERE nome_aluno='$nome' ");
						if (mysql_num_rows($res))	{
								$senha_res = mysql_fetch_array($res);
								if ($senha == $senha_res['senha']) {
										$_SESSION['user'] = $nome;
										header("Location: ".$_POST['uri']);
										exit;
								}
						}
				}
				// Se o usuario jah estah logado
				if (isset($_SESSION['user'])) {
						$this->login = $_SESSION['user'];
				} 		
				// Valida o acesso a esta pagina
		}
		public function loginForm() {
				if (isset($this->login)) {
						$T = "Usu&aacute;rio: $this->login";
						$T .="&nbsp;<a href='?logout=y'>logout</a>";
				} else {
						$T ="<form name=\"LoginForm\" action=\"index.php\" method=\"post\">";
						$T.="<input type=\"hidden\" id=\"action\" name=\"action\" value=\"login\">";
						$T.="<input type=\"text\" id=\"login\" name=\"login\" value=\"login\"><br>";
						$T.="<input type=\"password\" id=\"senha\" name=\"senha\" value=\"senha\"><br>";
						$T.="	<input type=\"hidden\" id=\"uri\" name=\"uri\" value=\"";
						$T.= $_SERVER['REQUEST_URI']; 
						$T.="\"><button type=\"submit\" id=\"LoginButton\" value=\"Submit\">login</button>";
						$T.="</form>";
				}
				return $T;
		}
}
 // Toda pagina precisa de um objeto de usuario:
$user = new User();

class Turma {
		private $id;
		public function getId() {
				return $this->id;
		}
		public function getNome() {
				$res = mysql_fetch_array(mysql_query("SELECT nome_turma FROM turma WHERE id_turma=$this->id"));
				return $res[0];
		}
		public function create($nome) {
				$n = mysql_real_escape_string($nome);
			mysql_query("INSERT INTO turma (nome_turma) VALUES ('$n');");
		}
		public function __construct($id) {
				$this->id = $id;
		}
}

class Exercicio {
		private $id;
		private $user;
		public function getId() {
			return $this->id;
		}
		public function nota() {
				if ($this->user->getLogin()) {
						$res = mysql_query("SELECT max(nota) FROM nota join aluno using (id_aluno) where nome_aluno = '".$this->user->getLogin()."' and id_exercicio=$this->id");
						if (mysql_num_rows($res))
						{
								$res = mysql_fetch_array($res);
								return $res[0];
						}
				}
		}
		public function nome() {
				$res = mysql_fetch_array(mysql_query("SELECT nome FROM exercicio WHERE id_exercicio=$this->id"));
				return $res[0];
		}
		public function html() {
				$res = mysql_fetch_array(mysql_query("SELECT html FROM exercicio WHERE id_exercicio=$this->id"));
				return $res[0];
		}
		public function precondicoes() {
				$res = mysql_fetch_array(mysql_query("SELECT precondicoes FROM exercicio WHERE id_exercicio=$this->id"));
				return $res[0];
		}
		public function prazo() {
				if ($this->user->getLogin()) {
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

class Teste {
	private $id_exercicio;
	private $ordem;
	public function __construct($id, $i) {
		$this->id_exercicio = $id;
		$this->ordem = $i;
	}
	public function condicao() {
		$res = mysql_fetch_array(mysql_query("SELECT condicao FROM teste WHERE id_exercicio=$this->id_exercicio AND ordem = $this->ordem"));
		return $res[0];
		}
	public function peso() {
		$res = mysql_fetch_array(mysql_query("SELECT peso FROM teste WHERE id_exercicio=$this->id_exercicio AND ordem = $this->ordem"));
		return $res[0];
		}
	public function dica() {
		$res = mysql_fetch_array(mysql_query("SELECT dica FROM teste WHERE id_exercicio=$this->id_exercicio AND ordem = $this->ordem"));
		return $res[0];
		}

}
function mres($q) {
	if(is_array($q)) 
		foreach($q as $k => $v) 
			$q[$k] = mres($v); //recursive
	elseif(is_string($q))
		$q = mysql_real_escape_string($q);
	return $q;
}
?>

