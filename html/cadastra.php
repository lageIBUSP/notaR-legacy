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

### comeca aqui
if (! $user->admin()) {
	echo "Acesso negado";
	exit;
}
$id = mysql_real_escape_string($_REQUEST['exerc']);
$X = new Exercicio($user, $id);

if (isset($_POST['ntestes'])) {$ntestes = $_POST['ntestes'];} elseif (!empty($id)) {
	$res = mysql_fetch_array(mysql_query("SELECT max(ordem) FROM teste WHERE id_exercicio=$id"));
	$ntestes = $res[0];}else {$ntestes = 10;}

?>
<h2>Cadastro de exerc&iacute;cios</h2>
<?php 
if (isset($_POST['submit']) AND $_POST['submit'] == "submit") {
$new = mres($_POST);
if (empty($id)) {
	$res = mysql_query("INSERT INTO exercicio (precondicoes, html, nome)
		VALUES (REPLACE('".
		$new['precondicoes']."', CHAR(13), ''), '".$new['html']."', '".$new['nome']."')");
	$my_id = mysql_insert_id();
	echo "Exerc&iacute;cio cadastrado ";	

} else
{
	$res = mysql_query("UPDATE exercicio SET precondicoes = REPLACE('".
		$new['precondicoes']."', CHAR(13), ''), html='".$new['html']."', nome='".$new['nome']."'
		WHERE id_exercicio=$id");
	$res = mysql_query("DELETE FROM teste WHERE id_exercicio=$id");
	echo "Exerc&iacute;cio alterado ";	
	$my_id = $id;
}
	$ok = true;
	for ($i=0, $c=0; $i < $ntestes; $i++) {
		$j = $i +1;
		if (! empty($new['condicao'][$i])) {
			$c ++;
			$T = new Teste();
			$ok = $ok AND $T->create($my_id, $j, $new['peso'][$i], $new['condicao'][$i],$new['dica'][$i]);
		}
	}
	echo "com $c testes.";
	if (! $ok) echo "<p>Falha ao cadastrar os testes!</p>";
	echo "Pr&oacute;ximos passos: <ul>
<li><a href='exercicio.php?exerc=$my_id'>Teste</a> se a corre&ccedil;&atilde;o funciona</li><li><a href='cadastra.php?exerc=$my_id'>Edite</a> as defini&ccedil;&otilde;es deste exerc&iacute;cio</li><li>Determine o <a href='prazos.php'>prazo</a> de entrega</li></ul>";
} 
else {

echo "<form name=\"cadastro\" action=\"#\" method=\"post\" enctype=\"multipart/form-data\">";
echo "<p>Para a descri&ccedil;&atilde;o dos campos e funcionamento do corretor, leia a <a href='http://www.lage.ib.usp.br/notaR/doku.php?id=cadastro'>ajuda</a>.";
echo "<br>Nome do exerc&iacute;cio:&nbsp;&nbsp;";
echo "<input type=\"text\" name=\"nome\"  style='width: 300px;' value=\"";
if (isset($_POST['nome'])) echo $_POST['nome'];
elseif (!empty($id)) echo $X->getNome();
echo "\">";
echo "<br>Precondi&ccedil;&otilde;es:&nbsp;";
echo "<br><textarea name=\"precondicoes\" rows=7 cols=80>";
if (isset($_POST['precondicoes'])) echo $_POST['precondicoes'];
elseif (!empty($id)) echo $X->getPrecondicoes();
echo "</textarea><br>HTML:<br><textarea name=\"html\" rows=7 cols=80>";
if (isset($_POST['html'])) echo $_POST['html'];
elseif (!empty($id)) echo $X->getHtml();
echo "</textarea><br>N&uacute;mero de testes:&nbsp;&nbsp;";
echo "<input type=\"text\" name=\"ntestes\" value=\"".$ntestes."\">";
echo "<button type=\"submit\" name=\"submit\" value=\"alterar\">alterar</button>";

echo "<h3>Testes</h3>";
echo "<table id='Cadastra'><tr><td><center><b>Ordem</b></center></td><td><center><b>Peso</b></center></td><td><center><b>Condi&ccedil;&atilde;o<center><b></td><td><center><b>Dica</b></center></td></tr>";
for ($i = 0; $i < $ntestes; $i ++) {
	if (!empty($id)) {$T = new Teste($id, $i+1);}
		echo "<tr>";
		echo "<td><center>".($i+1)."</center></td>";
		echo "</td><td><input type='text' name='peso[]' value='";
		if (isset($_POST['peso'][$i])) {echo $_POST['peso'][$i];} 
		elseif (!empty($id) AND $T->peso()) echo $T->peso();
		else {echo 1;}
		echo "'></td><td><input class='long' type='text' name='condicao[]' value='";
		if (isset($_POST['condicao'][$i])) {echo $_POST['condicao'][$i];}
		elseif (!empty($id)) echo $T->condicao();
		echo "'></td><td><input class='long' type='text' name='dica[]' value='";
		if (isset($_POST['dica'][$i])) {echo $_POST['dica'][$i];}
		elseif (!empty($id)) echo $T->dica();
		echo "'></td></tr>";
}
		echo "</table>";


echo "<button type=\"submit\" name=\"submit\" value=\"submit\">OK</button>";
echo "</form>";
}
?>
</div>
</body>
</html>
