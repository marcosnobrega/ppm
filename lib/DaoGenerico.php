<?php
class DaoGenerico{
	
	const FETCH_CLASS = 8;
	
	/**
	 * 
	 * 
	 * @var DaoIF
	 */
	private static $dao;
	
	/**
	 * 
	 * @var GerenteDeEntidade
	 */
	private $gerente;
	
	private function DaoGenerico(){
		$fabrica = Fabrica::getInstance();
		$this->gerente = GerenteDeEntidade::getInstance();
	}
	
	public static function getInstance(){
		if(!isset(self::$dao)){
			self::$dao = new DaoGenerico();
		}
		return self::$dao;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see comum/interfaces/DaoIF#adicionar()
	 */
	public function adicionar($objeto){
		
		if(!is_object($objeto)) return false;
		
		$conexao = ConnectionFactory::getConnection();				
		$resultado = $this->gerente->salvar($objeto);

		$stmt = $conexao->prepare($resultado["sql"]);
		$stmt->execute($resultado["valores"]);
		
		$objeto->setId($conexao->lastInsertId());
		
		if($conexao->errorCode() != 0000){
			$info = $conexao->errorInfo();
			throw new Exception($info[2]);
		}
		
		return $objeto->getId();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see comum/interfaces/DaoIF#deletar()
	 */
	public function deletar($objeto){
		
		if(!is_object($objeto)) return false;
		
		$conexao = ConnectionFactory::getConnection();
		$resultado = $this->gerente->deletar($objeto);
		$stmt = $conexao->prepare($resultado["sql"]);
		$retorno = $stmt->execute($resultado['valores']);
		
		// Se não houve erro ao deletar
		if($retorno){
			return true;
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see comum/interfaces/DaoIF#atualizar()
	 */
	public function atualizar($objeto){
		
		if(!is_object($objeto)) return false;
		
		$conexao = ConnectionFactory::getConnection();
		$resultado = $this->gerente->atualizar($objeto);
		$stmt = $conexao->prepare($resultado["sql"]);
		$resultado = $stmt->execute($resultado["valores"]);
		
		return $resultado;
	}
	
	
	/**
	 * 
	 * Retorna um conjunto de registros baseados na {@link Query} de entrada
	 * @param Query $query
	 * @return Query Retorna uma query com o total e os resgistros definidos
	 */
	public function buscar($query){
		
		if(!($query instanceof Query)) return false;
		if($token === false){
			global $token_global;
			$token = $token_global;
		}
		$conexao = ConnectionFactory::getConnection();
		$quantidade = $query->getQuantidade();
		$query->setQuantidade(false);
		$resultado = $this->gerente->buscar($query);
		$stmt = $conexao->query($resultado);
		
		if($conexao->errorCode() != 0000){
			$info = $conexao->errorInfo();
			throw new Exception($info[2]);
		}
		
		$query->setTotal($stmt->rowCount());
		$query->setQuantidade($quantidade);
		
		$resultado = $this->gerente->buscar($query);
		
		$stmt = $conexao->query($resultado);
		//Verifica se é para retornar os objetos baseado na classe deles ou na classe padrão stdClass
		$nomeDaClasse = "stdClass";
		if($query->retornarObjetosDaClasse()){
			$nomeDaClasse = $query->getClassName();
		}
		$query->setResultados($stmt->fetchAll(self::FETCH_CLASS, $nomeDaClasse));
		return $query;
	}
	
	/**
	 * Executa a instruçao sql especificada e retorna
	 * uma lista de objetos do tipo especificado em $classe
	 */
	public function executarQuery($sql, $classe){
		
		$conexao = ConnectionFactory::getConnection();
		$stmt = $conexao->query($sql);
		
		if($conexao->errorCode() != 0000){
			$info = $conexao->errorInfo();
			throw new Exception($info[2]);
		}
		
		if($classe){
			return $stmt->fetchAll(self::FETCH_CLASS, $classe);
		} else {
			return $stmt;
		}
	}
	
}
