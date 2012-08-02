<?php

/* 
 * Arquivo da classe GerenteDeEntidade
 */

/**
 * Classe GerenteDeEntidade
 * Esta classe manipula entidades,
 * manipulando atributos, métodos e documentação
 * e gerando o modelo relacional a partir de classes.
 *
 * @author Marcos Nóbrega
 */
class GerenteDeEntidade {
	
	private static $gerente;
	
	private function GerenteDeEntidade(){}
	
	public static function getInstance(){
		if(!isset(self::$gerente)){
			self::$gerente = new GerenteDeEntidade();
		}
		return self::$gerente;
	}

    /**
     * Método que cria a tabela referente a entidade no banco de dados
     * @param ReflectionClass $entidadeObjeto Um objeto da entidade a ser persistida
     * @return String A String SQL de criação da tabela para a entidade
     */
    private function criarTabela($classe){
        $criarTabelaSQL = "CREATE TABLE IF NOT EXISTS`".strtolower($classe->getName())."` (";
        $atributos = $classe->getProperties();
        $qtdeAtributos = count($atributos);
        foreach($atributos as $atributo){
            $criarTabelaSQL .= $this->getAtributoParaCriar($atributo);
            if($atributo !== $atributos[$qtdeAtributos-1]){
                $criarTabelaSQL .= ",";
            }
        }
        if($this->getChavePrimaria($classe) !== false){
            $criarTabelaSQL .= ", PRIMARY KEY (`".$this->getChavePrimaria($classe)."`)";
        }
        $criarTabelaSQL .= ");";
        return $criarTabelaSQL;
    }

    /**
     *
     * @param ReflectionProperty $atributo O Atributo a ser verificado se é Chave
     * @return boolean Se o atributo for chave primaria retorna true, senão retorna false
     */
    private function eChavePrimaria($atributo){
        if(strpos($atributo->getDocComment(),"@ID") !== false || $atributo->getName() == "id"){
            return true;
        }
        return false;
    }

    /**
     * Método que busca e retorna a chave primária de uma classe
     * @param ReflectionClass $classe A Classe da qual a chave deve ser retornada
     * @return String O nome da chave primária
     */
    private function getChavePrimaria($classe){
        $retorno = false;
        foreach($classe->getProperties() as $atributo){
            if($this->eChavePrimaria($atributo)){
                $retorno = $atributo->getName();
            }
        }
        return $retorno;
    }

    /**
     * Método para capturar o valor de um atributo do objeto
     * @param Object $objetoGenerico Uma instância
     * @param String $atributo O nome do atributo que quer o valor
     * @return mixed O valor do atributo na instância
     */
    private function getValorAtributo($objetoGenerico,$atributo){
        $getMethod = new ReflectionMethod(get_class($objetoGenerico),"get".$atributo);
        $retorno = $getMethod->invoke($objetoGenerico);
        $retorno = filter_var($retorno);
        
        if(!is_object($retorno) && !empty($retorno)){
        	return addslashes($retorno);	
        } else {
        	return $retorno;
        }
    }

    /**
     * Método que constrói a string SQl de inserção de um objeto no banco de dados
     * @param Object $objetoGenerico O objeto a ser persistido
     * @return String Um string SQL de inserção do objeto no bd
     */
    public function salvar($objetoGenerico){
    	
        $objetoRefletido = new ReflectionObject($objetoGenerico);
        $insertSQL = "INSERT INTO `".$this->getNomeDaTabela($objetoGenerico)."` ";
        $atributos = $objetoRefletido->getProperties();
        $qtdeAtributos = count($atributos);
        foreach($atributos as $atributo){
        	if(strpos($atributo->getName(),"__") === 0) 
        		continue;
        		
      		if ($atributo->getName() != "id") {
      			$colunas[] = $atributo->getName();
      			$coringas[] = "?";
      			      			
      			$valor = $this->getValorAtributo($objetoGenerico, $atributo->getName());
      			
      			if($valor !== NULL){
					$values[] = $valor;
				}
		        else{
		        	 $values[] = null;
		        }
      		}
        	
        }
        $insertSQL .= "(".implode(",", $colunas).") VALUES(".implode(",",$coringas).");";
        
        $resultado["sql"] = $insertSQL;
        $resultado["valores"] = $values;
        
        return $resultado;
    }

    /**
     * Método que constrói a string SQL de deleção de um objeto do banco de dados
     * @param Object $objetoGenerico O objeto para ser deletado do banco de dados
     * @return String A string SQL de deleção
     */
    public function deletar($objetoGenerico, $token=false){
        $objetoRefletido = new ReflectionObject($objetoGenerico);
        $deleteSQL = "DELETE FROM `".$this->getNomeDaTabela($objetoGenerico,$token)."` ";
        $atributoChave = $this->getChavePrimaria($objetoRefletido);
        $deleteSQL .= "WHERE $atributoChave = ?";
        
        $resultado["sql"] = $deleteSQL;
        $resultado["valores"] = array($this->getValorAtributo($objetoGenerico, $atributoChave));
        
        return $resultado;
    }
    
    /**
     * 
     * Constrói a string SQL para atualizar o objeto no banco de dados
     * @param $objetoGenerico O objeto a ser atualizado
     * @return string
     */
    public function atualizar($objetoGenerico, $token=false){
        $objetoRefletido = new ReflectionObject($objetoGenerico);
        $updateSQL = "UPDATE `".$this->getNomeDaTabela($objetoGenerico,$token)."` SET ";
        $atributos = $objetoRefletido->getProperties();
        $qtdeAtributos = count($atributos);
        foreach($atributos as $atributo){
        	if(strpos($atributo->getName(),"__") === 0) continue;
        	
            if(!$this->eChavePrimaria($atributo)){
	            $colunas[] = $atributo->getName()." = ?";
      			
            	$valor = $this->getValorAtributo($objetoGenerico, $atributo->getName());
      
				if($valor !== NULL){
					$values[] = $valor;
				}
	        	else{
	        		$values[] = null;
	        	}
	    
            }
        }
        $updateSQL .= implode(",",$colunas);
        $atributoChave = $this->getChavePrimaria($objetoRefletido);
        $updateSQL .= " WHERE $atributoChave = ?";
        $values[] = $this->getValorAtributo($objetoGenerico, $atributoChave);
        
        $resultado["sql"] = $updateSQL;
        $resultado["valores"] = $values;
        
        return $resultado;
    }

    /**
     * Método que retorna um objeto pela chave primária
     * @param String $nomeDaClasse O nome da classe a qual o objeto referencia
     * @param mixed $chave O valor da chave do objeto
     * @return String SQL
     */
    public function getObjetoChave($nomeDaClasse,$chave){
        $classeRefletida = new ReflectionClass($nomeDaClasse);
        $atributoChave = $this->getChavePrimaria($classeRefletida);
        $getSQL = "SELECT * FROM `".strtolower($classeRefletida->getName())."` ";
        $getSQL .= "WHERE $atributoChave = ?";
        
        $resultado["sql"] = $getSQL;
        $resultado["valores"] = array($chave);
        
        return $resultado;
    }

    /**
     * 
     * Retorna uma string SQL baseada na {@link Query} de entrada
     * @param Query $query
     * @return string A SQL de consulta
     */
    public function buscar($query, $token=false){
    	
    	$camposRetorno = "*";
    	
    	if($query->getConsiderarCamposOcultos()){
	    	$objetoRefletido = new ReflectionClass($query->getNomeClasse());
			$properties = $objetoRefletido->getProperties();
			$hidden = array();
			foreach($properties as $property){
				if(strpos($property->getName(),"__") === 0) continue;
				
				if(strpos($property->getDocComment(),"@hidden") === false){
		        	$camposRet[] = $property->getName();    
		        }
			}
			$camposRetorno = implode(",",$camposRet);
    	}
    	
    	$sqlBusca = "SELECT $camposRetorno FROM `".$this->getNomeDaTabela($query->getNomeClasse(),$token)."`";
    	
    	$values[] = array();
    	
    	if($query->getCamposPesquisa()){
    		$campos = array();
    		foreach($query->getCamposPesquisa() as $campo => $valor){
    			$multicampos = explode(",", $campo);
    			$operador = "=";
    			$adicionarAspas = true;
    			if(is_array($valor)){
    				$operador = $valor[1];
    				if(isset($valor[2]) && $valor[2]){
    					$collate = $valor[2];
    				}
    				$adicionarAspas = (isset($valor[3]) && $valor[3] === false) ? false : true;
    				$valor = $valor[0];
    			}
    			if(!is_int($valor) && !is_null($valor) && strpos($valor,"(") !== 0){
    				$valor = $adicionarAspas ? "'$valor'" : $valor;
    				if(isset($collate)){
    					$valor .= " COLLATE $collate";
    				}
    			}
    			if(is_null($valor)){
    				$valor = 'IS NULL';
    				$operador = "";
    			}
    			if(count($multicampos)>1){
    				$campo = array();
    				foreach($multicampos as $multicampo){
    					$campo[] = "$multicampo $operador $valor";
    				}
    				$campos[] = "(".implode(" OR ",$campo).")";
    			} else {
    				$campos[] = "$campo $operador $valor";
    			}
    		}
    		$sqlBusca .= " WHERE ".implode(" AND ",$campos);
    	}
    	
    	if($query->getGroupby()){
    		$sqlBusca .= " GROUP BY {$query->getGroupby()}";
    	}
    	if($query->getOrder()){
    		$campos = array();
    		foreach($query->getOrder() as $campo => $tipo){
    			$campos[] = "$campo $tipo";
    		}
    		$sqlBusca .= " ORDER BY ".implode(",",$campos);
    	}
    	
    	if($query->getQuantidade()) {
    		$sqlBusca .= " LIMIT ".$query->getIndiceInicial().",".$query->getQuantidade();
    		$values[] = $query->getIndiceInicial();
    		$values[] = $query->getQuantidade();
    	}
    		
    	$resultado["sql"] = $sqlBusca;
    	$resultado["valores"] = $values;
    		
    	return $sqlBusca;
    }
    
    /**
     * Retonar variáveis estaticas da classe
     * @param unknown_type $objeto 
     */
    public function getValorEstatico($classe, $staticVal="__META_INFO"){        	
    	$reflection = new ReflectionClass($classe);
	    $static = $reflection->getStaticPropertyValue($staticVal);
    	return $static;
    }
    
    private function getNomeDaTabela($objeto){
    	if(is_object($objeto)){
    		$classe = get_class($objeto);
    	}elseif(is_string($objeto)){
    		$classe = $objeto;
    	}
    	
    	$tabela = strtolower($classe);
    	
    	return $tabela;
    	
    }
    
}
?>
