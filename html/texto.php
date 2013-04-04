<?php
require_once("config.php");
require_once("class/aluno.php");
require_once("class/nota.php");

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
## COMECA AQUI
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
if (isset($_POST['aluno'])) 
	$aluno = mysql_real_escape_string($_POST['aluno']);
else {
		$A = mysql_fetch_array(mysql_query("SELECT id_aluno FROM aluno where nome_aluno = (SELECT MIN(nome_aluno) FROM aluno WHERE id_turma=".$turma->getId(),")"));
		$aluno = $A[0];
}
if (isset($_POST['exercicio'])) 
	$exercicio = mysql_real_escape_string($_POST['exercicio']);
else {
		$E = mysql_fetch_array(mysql_query("SELECT id_exercicio FROM exercicio where nome_exercicio = (SELECT MIN(nome_exercicio) FROM exercicio)"));
		$exercicio = $E[0];

}
if (isset($_POST['texto'])) $texto = mysql_real_escape_string($_POST['texto']);
else $texto = "";
?>
<h2>Busca em texto</h2>
<p>Encontre o texto submetido pelos alunos para cada exerc&iacute;cio.</p>
<form action='texto.php' method='POST'>
<p>Escolha a turma:<?php echo SelectTurma(); ?></p>
<p>Escolha um exerc&iacute;cio: 
	<select id='exercicio' name='exercicio'>
<?php
$lista_exercicio = mysql_query("SELECT id_exercicio FROM exercicio ORDER BY nome ASC");

while ($T = mysql_fetch_array($lista_exercicio)) {
	$loop_exercicio = new Exercicio(NULL, $T[0]);
	echo "	<option value=".$loop_exercicio->getId();
	if($loop_exercicio->getId() == $exercicio) echo " selected";
	echo ">".$loop_exercicio->getNome()."</option>";
}

?>
</select></p>
<ul><li>Escolha um aluno para ver todas as tentativas OU</li>
<li>Digite um texto para procurar nas respostas</li></ul>
	<select id='aluno' name='aluno'>
<?php
$lista_alunos = mysql_query("SELECT id_aluno FROM aluno WHERE id_turma=".$turma->getId()." ORDER BY nome_aluno ASC");

while ($T = mysql_fetch_array($lista_alunos)) {
	$loop_aluno = new Aluno($T[0]);
	echo "	<option value=".$loop_aluno->getId();
	if($loop_aluno->getId() == $aluno) echo " selected";
	echo ">".$loop_aluno->getNome()."</option>";
}

?>
</select> OU
<input type="text" name="texto" value="<?php echo $texto; ?>">
<button type="submit" name="submit" value="busca">Busca</button>
</form>
<table>
<tr>
<?php if ($texto != "") echo"<td>Aluno</td>";?><td>Data</td></td><td>Nota</td><td>Texto</td></tr>

<?php
if ($texto == "") 
	$lista_exs = mysql_query("SELECT id_nota FROM nota WHERE id_exercicio=$exercicio AND id_aluno=$aluno ORDER BY data ASC");
else 
	$lista_exs = mysql_query("SELECT id_nota FROM nota WHERE id_exercicio=$exercicio AND texto LIKE '%$texto%' ORDER BY data ASC");

while ($N = mysql_fetch_array($lista_exs)) {
	$ex = new Nota($N[0]);
	echo "<tr>";
	if($texto != "") echo "<td>".$ex->getNomeAluno()."</td>";
	echo "<td>".$ex->getData()."</td><td>".$ex->getNota()."</td><td>".$ex->getTexto()."</td></tr>";
}
?>
</table>
</div>
</body>
</html>
