<?php
require_once("config.php");
require_once("class/aluno.php");
require_once("class/nota.php");

class User extends Aluno {
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
				ini_set("session.cookie_lifetime", 360000);
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
$USER = $user; //migrando para novo padrao....

require_once("class/turma.php");
require_once("class/exercicio.php");

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
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
?>
<h2>Relat&oacute;rio de notas</h2>
<p>Escolha a turma: <?php echo SelectTurma(); ?></p>

<table>
<tr><th>Aluno</th>
<?php
$lista_exs = mysql_query("SELECT DISTINCT id_exercicio FROM exercicio JOIN nota USING (id_exercicio) JOIN aluno USING (id_aluno) WHERE id_turma=".$TURMA->getId()." ORDER BY nome");
$i = 0;
while ($E = mysql_fetch_array($lista_exs)) {
	$ex[$i] = new Exercicio($E[0]);
	$n = $ex[$i]->getNome();
	echo "<th>".substr($n,0,strpos($n, " "))."</th>";
	$i++;
}
echo "	</tr>";

foreach (ListAlunos($TURMA) as $aluno) {
		echo "<tr><td>".$aluno->getNome()."</td>";
	foreach ($ex as $E) {
			echo "<td>".$E->getNota($aluno)."</td>";
	}
		echo "</tr>";
}
?>
</table>
</div>
</body>
</html>
