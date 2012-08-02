<?php
class BD {
	
	private $conexao;
	
	public function BD($servidor,$usuario,$senha,$bancodedados){
		$this->conexao = mysql_connect($servidor,$usuario,$senha);
		mysql_select_db($bancodedados,$this->conexao);
	}
	
	public function query($sql){
		$stmt = new BDStatement();
		$stmt->setResultados(mysql_query($sql));
		return $stmt;
	}
	
	public function lastInsertId(){
		return mysql_insert_id($this->conexao);
	}
	
}
?>