<?php
class BDStatement {
	
	private $resultados;
	
	public function getResultados(){
		return $this->resultados;
	}
	
	public function setResultados($resultados){
		$this->resultados = $resultados;
	}
	
	public function rowCount(){
		return mysql_num_rows($this->resultados);
	}
	
	public function fetchAll($fetchType=false,$class=""){
		if(isset($this->resultados) == false) return false;
		$fabrica = Fabrica::getInstance();
		$fetched = array();
		while($atual = mysql_fetch_assoc($this->resultados)){
			$objeto = $atual;
			if($fetchType==8 && empty($class) == false){
				$metodo = "getInstance$class";
				$objeto = call_user_method($metodo, $fabrica);
				$reflection = new ReflectionObject($objeto);
				foreach($reflection->getProperties() as $atributo){
					if(!eregi("NP", $atributo->getDocComment())){
						try{
							$method = new ReflectionMethod($class, "set".$atributo->getName());
							$method->invoke($objeto, $atual[strtolower($atributo->getName())]);
						} catch(Exception $e){
							//Nada
						}
					}
				}
			}
			$fetched[] = $objeto;
		}
		return $fetched;
		
	}
	
}