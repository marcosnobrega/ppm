<?php
class FacadeGenerico{
	
	private static $facade;
	
	/**
	 * 
	 * 
	 * @var DaoGenerico
	 */
	private $dao;
	
	private function FacadeGenerico(){
		$fabrica = Fabrica::getInstance();
		$this->dao = $fabrica->getInstanceDaoGenerico();
	}
	
	public static function getInstance(){
		if(!isset(self::$facade)){
			self::$facade = new FacadeGenerico();
		}
		return self::$facade;
	}
	
	/**
	 * 
	 * Adiciona um objeto qualquer ao banco de dados
	 * @param $objeto
	 */
	public function adicionar($objeto){
//		echo get_class($objeto);
		return $this->dao->adicionar($objeto);
	}
	
	/**
	 * 
	 * Deleta um objeto qualquer do banco de dados
	 * @param $objeto
	 */
	public function deletar($objeto){
		return $this->dao->deletar($objeto);
	}
	
	/**
	 * 
	 * Atualiza um objeto qualquer no banco de dados
	 * @param $objeto
	 */
	public function atualizar($objeto){
		return $this->dao->atualizar($objeto);
	}
	
	/**
	 * 
	 * Realiza um busca baseado nos paramêtros e retorna uma {@link Query} com os resultados
	 * @param string $nomeClasse
	 * @param array $camposPesquisa Array com chave indicando campo e valor indicando o valor
	 * @param int $pagina A o número da página referência da paginação
	 * @param int $quantidade Quantidade de registros retornados pela consulta
	 * @param array $order Array com campos para ordenação, indice é o nome do campo e valor corresponde a DESC ou ASC
	 * @param string $groupby Nome do campo para agrupar os resultados, trará um resultado de cada campo agrupado
	 * @return Query
	 */
	public function buscar($nomeClasse,$camposPesquisa=false,$pagina=false,$quantidade=false,$order=false,$groupby=false,$retornarObjetosDaClasse=true){	
		$query = Query::getInstance();
		$query->setNomeClasse($nomeClasse);
		if($camposPesquisa){
			$query->setCamposPesquisa($camposPesquisa);
		}
		if($quantidade){
			$pag = $pagina ? $pagina : 1;
			$indiceInicial = $pag-1;
			$indiceInicial = $indiceInicial*$quantidade;
			$query->setIndiceInicial($indiceInicial);
			$query->setQuantidade($quantidade);
		}
		if($order){
			$query->setOrder($order);
		}
		if($groupby){
			$query->setGroupby($groupby);
		}
		$query->setRetornarObjetosDaClasse($retornarObjetosDaClasse);
		$query->setConsiderarCamposOcultos($considerarCamposOcultos);
		return $this->dao->buscar($query, $token);
	}
	
	/**
	 * Executa a instrução sql especificada e retorna
	 * uma lista de objetos do tipo especificado em $classe
	 */
	public function executarQuery($sql, $classe=false){
		return $this->dao->executarQuery($sql, $classe);
	}
	
}