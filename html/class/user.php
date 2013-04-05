<?php
require_once("config.php");

class User extends Aluno {
	public function __construct() {
		global $mysqli;
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
			$res = $mysqli->prepare("SELECT 1 FROM aluno WHERE nome_aluno = ? AND senha = SHA1(?)");
			$res->bind_param('ss',$_POST['login'], $_POST['senha']);
			$res->execute();
			if ($res->fetch())	{
				$_SESSION['user'] = $_POST['login'];
				header("Location: ".$_POST['uri']);
				exit;
			}
			# Se chegou aqui e nao caiu no exit, eh porque algo deu errado no login
			global $loginerror;
			$loginerror = "<div id='Erro'><h2>Erro!</h2><p>Verifique se seu nome de usu&aacute;rio e senha est&atilde;o corretos.</p></div>";
		}
		// Se o usuario jah estah logado
		if (isset($_SESSION['user'])) {
			$res = $mysqli->prepare("SELECT id_aluno, admin, id_turma FROM aluno WHERE nome_aluno=?");
			$res->bind_param('s',$_SESSION['user']);
			$res->execute();
			$res->bind_result($this->id, $this->admin, $this->turma);
			$res->fetch();
			$this->nome=$_SESSION['user'];
			$this->login=$_SESSION['user'];
		} 		
	}
	public function loginForm() {
		if (isset($this->nome)) {
			$T = "<div style='text-align:right'>Usu&aacute;rio: $this->nome";
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
$USER = new User(); 

?>
