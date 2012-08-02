<?php
/**
 * F�brica de conex�o do sistema, esta f�brica fornecer� as conex�es com o banco de dados
 * @author Marcos Nobrega
 *
 */
class ConnectionFactory{
	
	/**
	 * 
	 * @var PDO
	 */
	private static $conexao;
	
	private static $DB_DRIVER = "mysql";
	private static $DB_SERVER = "localhost";
	private static $DB_PORT = 3306;
	private static $DB_NAME = "database_name";
	private static $DB_USERNAME = "username";
	private static $DB_PASSWORD = "password";
	
	/**
	 * Retorna uma conexão ao banco de dados com base no contexto.
	 * @param unknown_type $contexto Contexto a ser verificado LOCAL ou GLOBAL.
	 * @param unknown_type $token Identificação do usuário.
	 */
	public static function getConnection(){
		try{			
			if( isset( self::$conexao ) == false ){
				
				$fabrica = Fabrica::getInstance();
				$gerenteConfiguracaoBD = $fabrica->getInstanceGenerico("GerenteConfiguracaoBD");
				$configBD = $gerenteConfiguracaoBD->getConfiguracaoBD();
				
				if(class_exists("PDO")){
					self::$conexao = new PDO(
						self::$DB_DRIVER.":dbname=".self::$DB_NAME.";
						host=".self::$DB_SERVER.";
						port=".self::$DB_PORT,
						self::$DB_USERNAME,
						self::$DB_PASSWORD
					);
				} else {
					self::$conexao = new BD(self::$DB_SERVER,self::$DB_USERNAME,self::$DB_PASSWORD,self::$DB_NAME);
				}
				self::$conexao->query("SET NAMES utf8");
			}
			return self::$conexao;
		} catch(Exception $e){
			die($e->getMessage());
		}
	}
	
}
?>
