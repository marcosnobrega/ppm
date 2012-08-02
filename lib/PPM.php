<?php

require dirname(__FILE__)."/BD.php";
require dirname(__FILE__)."/BDStatement.php";
require dirname(__FILE__)."/ConnectionFactory.php";
require dirname(__FILE__)."/GerenteDeEntidade.php";
require dirname(__FILE__)."/DaoGenerico.php";
require dirname(__FILE__)."/FacadeGenerico.php";
require dirname(__FILE__)."/Query.php";

/**
 * Lib to persist objects in MySQL databases
 * you can create any object in a database table with the same propeties named equal in both
 * and then just call the functions in the class PPM like save, update, delete and search
 * with the object to persist or search. The table must have the same name of the class
 * that the object is an instance of. Every class and table must have a property named as
 * id, that will be the primary key index.
 * @author marcosnobregajr@gmail.com
 *
 */

class PPM{
	
	/**
	 * Function that saves an object in the MySQL database
	 * @param object $object The object to be saved in the database
	 */
	public function save($object){
		$facade = FacadeGenerico::getInstance();
		return $facade->adicionar($object);
	}
	
	
	/**
	 * Function that updates an object in the database
	 * @param object $object the object to be updated
	 * @return boolean
	 */
	public function update($object){
		$facade = FacadeGenerico::getInstance();
		return $facade->atualizar($object);
	}
	
	/**
	 * Function that deletes an object from the database
	 * @param object $object The object to be deleted from the database
	 */
	public function delete($object){
		$facade = FacadeGenerico::getInstance();
		return $facade->deletar($object);
	}
	
	/**
	 * Function that searches for a record based on input params
	 * @param string $className The name of the class related to the object to find
	 * @param array $params An array mapping the property and the value to find like array('id'=>3)
	 * @param int $pageOffset The number of the page used to paginate the results
	 * @param int $quantity The quantity of records to be fetched
	 * @param array $order An array mapping property the sort mode like array('id'=>'ASC')
	 */
	public function search($className, $params, $pageOffset, $quantity, $order){
		$facade = FacadeGenerico::getInstance();
		$query = $facade->buscar($className, $params, $pageOffset, $quantity, $order);
		if($query->getTotal() > 0){
			return $query->getResultados();
		} else {
			return false;
		}
	}
	
}