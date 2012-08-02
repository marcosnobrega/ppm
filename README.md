ppm
===

PHP Persistency Manager - A PHP lib to persist objects in MySQL databases

Configuration
===

In order to use the lib, you must set up your database information the file lib/ConnectionFactory.php.
Set the server, port, db name, username and password

Usage
===

To use the lib is very simple, you just need to create an instance of PPM and use the functions save, update, delete and search.

Creating classes and database table
===

To use the persistence, you need to create a class and a database table with the same name and attributes, 
and you class must have an attribute named as id, that will be the primary key index. And every class must have
a function __toString() that returns the id
	
Example:

class Foo{

	private $id;
	private $bar;
	
	public function getId(){ return $this->id; }
	
	public function setId($id){ $this->id = $id; }
	
	public function getBar(){ return $this->bar; }
	
	public function setBar($bar){ $this->bar = $bar; }
	
	public function __toString(){ return (string)$this->id; }

}