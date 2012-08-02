<?php
class Query{
	
	/**
	 * 
	 * Nome da classe/tabela para realizar a busca
	 * @var $nomeClasse string
	 */
	private $nomeClasse;
	/**
	 * 
	 * Os campos de referência para a busca
	 * Um array, chave => valor onde a chave é o campo e o valor, o valor!
	 * @var $camposPesquisa array
	 */
	private $camposPesquisa = array();
	/**
	 * 
	 * Indice inicial da consulta, começa em 0 até o total de registros
	 * @var $indiceInicial int
	 */
	private $indiceInicial = 0;
	/**
	 * Quantidade de resultados retornados pela consulta
	 * @var $quantidade int
	 */
	private $quantidade;
	/**
	 * 
	 * Os campos de referência para ordenar a busca
	 * Um array, chave => valor onde a chave é o campo e o valor, o tipo de ordenacao ASC ou DESC
	 * @var $order array
	 */
	private $order = array();
	/**
	 * 
	 * Campo de referência para agrupar os resultados, trazendo um único resultado de cada campo
	 * @var $groupby string
	 */
	private $groupby;	
	/**
	 * Total de registros encontrados pela consulta
	 * este valor é determinado pela consulta
	 * @var $total int
	 */
	private $total;
	/**
	 * 
	 * A lista de resultados retornados pela consulta
	 * @var $resultados array
	 */
	private $resultados;
	/**
	*
	* Define se na consulta deve-se retornar objetos como instância da Classe buscada
	* @var $usarObjetosDaClasse boolean
	*/
	private $retornarObjetosDaClasse = true;
	
	private $considerarCamposOcultos = false;
	
	private function Query(){}
	
	public static function getInstance(){
		return new Query();
	}

	public function getNomeClasse(){
		return $this->nomeClasse;
	}
	
	public function setNomeClasse($nomeClasse){
		$this->nomeClasse = $nomeClasse;
	}
	
	public function getCamposPesquisa(){
		return $this->camposPesquisa;
	}
	
	public function setCamposPesquisa($camposPesquisa){
		$this->camposPesquisa = $camposPesquisa;
	}
	
	public function getIndiceInicial(){
		return $this->indiceInicial;
	}
	
	public function setIndiceInicial($indiceInicial){
		$this->indiceInicial = $indiceInicial;
	}
	
	public function getQuantidade(){
		return $this->quantidade;
	}
	
	public function setQuantidade($quantidade){
		$this->quantidade = $quantidade;
	}
	
	public function getOrder(){
		return $this->order;
	}
	
	public function setOrder($order){
		$this->order = $order;
	}
	
	public function getGroupby(){
		return $this->groupby;
	}
	
	public function setGroupby($groupby){
		$this->groupby = $groupby;
	}
		
	public function getTotal(){
		return $this->total;
	}
	
	public function setTotal($total){
		$this->total = $total;
	}
	
	public function getResultados(){
		return $this->resultados;
	}
	
	public function setResultados($resultados){
		$this->resultados = $resultados;
	}
	
	public function retornarObjetosDaClasse(){
		return $this->retornarObjetosDaClasse;
	}
	
	public function setRetornarObjetosDaClasse($retornar){
		$this->retornarObjetosDaClasse = $retornar;
	}
	
	public function getConsiderarCamposOcultos(){
		return $this->considerarCamposOcultos;
	}
	
	public function setConsiderarCamposOcultos($valor){
		$this->considerarCamposOcultos = $valor;
	}
	
}
?>