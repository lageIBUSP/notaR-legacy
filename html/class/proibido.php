<?php
require_once("config.php");

class Proibidos {
	private $id, $palavra, $exercicio, $hard;
	public function __construct($id=null) { 
		global $mysqli;
		if ($id) {
			$res = $mysqli->prepare("SELECT palavra, id_exercicio, hard FROM proibido WHERE id_proibido = ?");
			$res->bind_param('i', $id);
			$res->execute();
			$res->bind_result($this->palavra, $this->exercicio, $this->hard);
			$res->fetch();
			$this->id= $id; 
		}
	}
	public function getPalavra() {return $this->palavra;}
	public function getHard() {return $this->hard;}
	public function getExercicio() {return $this->exercicio;}
	public function getId() {return $this->id;}
	public function create($palavra, $exercicio = null) {
		global $mysqli;
        if ($exercicio) {
    		$res = $mysqli->prepare("INSERT INTO proibido (id_exercicio, palavra) VALUES (?, ?)");
	    	$res->bind_param('is', $exercicio, $palavra);
        } else {
    		$res = $mysqli->prepare("INSERT INTO proibido (palavra) VALUES (?)");
            $res->bind_param('s', $palavra);
        }
		$res->execute();
		if ($mysqli->error) return false;
		return true;
	}
    public function remove() {
        global $mysqli;
        if ($this->hard == 1 or !empty($this->exercicio))
            return false;
        $res = $mysqli->prepare("DELETE FROM proibido WHERE id_proibido = ?");
        $res->bind_param('i', $this->id);
		$res->execute();
		if ($mysqli->error) return false;
		return true;
    }
	public function pass($string, $id_ex) {
		global $mysqli;
		$res = $mysqli->prepare("SELECT palavra FROM proibido WHERE id_exercicio =? OR id_exercicio IS NULL");
		$res->bind_param('i', $id_ex);
		$res->execute();
		$res->bind_result($palavra);
		while ($res->fetch()) {
			if (strpos($string, $palavra) !== FALSE ) return $palavra;
		}
		# Se chegou aqui, nada proibido
		return FALSE;
	}
}

function ListProibidos($ex = false) {
	global $mysqli;
    if ($ex) 
        $res = $mysqli->query("SELECT id_proibido FROM proibido WHERE id_exercicio IS NULL ORDER BY id_proibido ASC");
    else
        $res = $mysqli->query("SELECT id_proibido FROM proibido WHERE id_exercicio IS NOT NULL ORDER BY id_proibido ASC");
	$a = array();
	while ($row = $res->fetch_assoc()) 
		array_push($a, new Proibidos($row['id_proibido']));
	return $a;
}

?>
