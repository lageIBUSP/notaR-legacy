<?php

define('RSERVE_HOST','localhost');
$basedir = '/var/www/rserve';
ini_set('display_errors', 'On');

error_reporting(E_ALL);

ini_set('display_errors', 'On');
error_reporting(E_ALL);


$CONFIG['dbserver'] = 'localhost';
$CONFIG['dbuser'] = 'notaR';
$CONFIG['dbpass'] = 'notaRpw';
$CONFIG['dbname'] = 'notaR';

mysql_connect($CONFIG['dbserver'], $CONFIG['dbuser'], $CONFIG['dbpass']);
mysql_select_db($CONFIG['dbname']);
mysql_set_charset("UTF-8");

$mysqli = new mysqli("localhost", "notaR", "notaRpw", "notaR");

require_once("class/aluno.php");
class Nota {
	private $id;
	public function getNomeAluno() {
		$res = mysql_fetch_array(mysql_query("SELECT nome_aluno FROM aluno JOIN nota using (id_aluno) where id_nota=$this->id"));
		return $res[0];
	}
	public function getNota() {
		$res = mysql_fetch_array(mysql_query("SELECT nota FROM nota where id_nota=$this->id"));
		return $res[0];
	}
	public function getData() {
		$res = mysql_fetch_array(mysql_query("SELECT data FROM nota where id_nota=$this->id"));
		return $res[0];
	}
	public function getTexto() {
		$res = mysql_fetch_array(mysql_query("SELECT texto FROM nota where id_nota=$this->id"));
		return nl2br($res[0]);
	}
	public function __construct($id) { $this->id=$id;}
}

class User {
		private $login;
		public function getLogin() {
			if (isset($this->login)) return $this->login;
			return "";
		}
		public function admin () {
			$res = mysql_fetch_array(mysql_query("SELECT admin FROM aluno where nome_aluno='".$this->login."'"));
			return $res[0];
		}
		public function novaSenha ($senha) {
			mysql_query("UPDATE aluno SET senha=SHA('$senha') WHERE nome_aluno='".$this->login."'");
		}
		public function __construct() {
			if (!isset($_SESSION)) 
			{
				ini_set("session.cookie_lifetime", 3600);
				session_start();
			}
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
						# Se chegou aqui e nao caiu no exit, eh porque algo deu errado no login
						global $loginerror;
						$loginerror = "<div id='Erro'><h2>Erro!</h2><p>Verifique se seu nome de usu&aacute;rio e senha est&atilde;o corretos.</p></div>";
				}
				// Se o usuario jah estah logado
				if (isset($_SESSION['user'])) {
						$this->login = $_SESSION['user'];
				} 		
				// Valida o acesso a esta pagina
		}
		public function loginForm() {
				if (isset($this->login)) {
						$T = "<div style='text-align:right'>Usu&aacute;rio: $this->login";
						$T .="&nbsp;<a href='?logout=y'>logout</a>";
						$T .="<br>alterar <a href='senha.php'>senha</a></div>";
				} else {
						$T ="<form name=\"LoginForm\" action=\"index.php\" method=\"post\">";
						$T.="<input type=\"hidden\" id=\"action\" name=\"action\" value=\"login\">";
						$T.="<input type=\"text\" id=\"login\" name=\"login\" placeholder=\"login\"><br>";
						$T.="<input type=\"password\" id=\"senha\" name=\"senha\" placeholder=\"senha\"><br>";
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

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script language="javascript" src="java.js"></script>
		<meta charset="iso-8859-1" />
		<meta name="description" content="Um sistema para notas automatizadas em cursos que utilizam a linguagem R" />
		<title>notaR</title>
	</head>
<body onload="defch()">
	<div id="Top">
		<div style="float:left">
			<h1><a href="index.php">notaR</a></h1>
			<p>Um sistema para notas automatizadas em cursos que 
			utilizam a linguagem R</p>
		</div>
		<div style="float:right">
			<br>&nbsp;
			<?php echo $user->loginForm(); ?>
		</div>
<div style=" width: 100%; height: 1px; clear:both"></div>
	</div>
	<div id="MainDiv">
<?php
if(isset($loginerror))  echo $loginerror; 

### comeca aqui!
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}

###### Codigo aqui
if(isset($_POST['submit']) AND $_POST['submit']=="altera") {
	$aluno = new Aluno($_POST['id']);
	if(isset($_POST['admin'])) $admin=1; else $admin=0;
	echo $aluno->altera($_POST['nome'], $admin, $turma, $_POST['senha']);
}
if(isset($_POST['submit']) AND $_POST['submit']=="insere") {
	$arr=preg_split("/\r\n|\r|\n/",$_POST['novos']);
	$senha = $_POST['senha'];
	if (empty($senha)) { echo "<p>Voc&ecirc; deve informar uma senha!</p>";}
	else {
		$erros ="";
		foreach ($arr as $novo) {
			$aluno = new Aluno();
			echo $aluno->create($novo, $turma, $senha);
		}
	}
}
?>
<h2>Cadastro de alunos</h2>
<p>Escolha a turma</p>
<form action='alunos.php' method='POST'>
<?php echo SelectTurma($turma); ?>

<p>Alunos cadastrados:</p>
<table><tr><td>Admin</td><td>Login</td><td>Notas</td><td>Editar</td>
<?php
foreach (ListAlunos($turma) as $aluno) {
	echo "<tr><td>";
	if ($aluno->admin()) echo "<img src='img/check.png'>"; else echo "&nbsp;";
	echo "</td><td>".$aluno->getNome()."</td><td>".$aluno->numNotas()."</td><td>";
	echo "<a href='aluno.php?id=".$aluno->getId()."'><img src='img/pen.png'></a></td></tr>";
}
?>
</table>
<p>Para cadastrar novos alunos nesta turma, preencha os logins na caixa de texto abaixo, um por linha:</p>
<textarea name="novos" rows=5 cols=70>
</textarea>
<p>Senha: <input type="text" name="senha">
<br><button type='submit' name='submit' value='insere'>Inserir</button></p>

</form>
</div>
</body>
</html>
