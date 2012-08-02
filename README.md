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

Saving an object from class Foo
===

To save an object is very simple, just create an instance like:

$foo = new Foo();
$foo->setBar('myfoo');

$ppm = new PPM();
$foo->setId($ppm->save($foo));

echo $foo->getId();

Updating an object
===

To update an object you just need to retrieve the object set the new values and call the update function

$ppm = new PPM();
$foo = reset( $ppm->search( 'Foo', array('id'=>1) ) );

$foo->setBar('new value');

$ppm->update($foo);

Deleting an object
===

To delete an object you just need to retrieve the object and call the delete function

$ppm = new PPM();
$foo = reset( $ppm->search( 'Foo', array('id'=>1) ) );

$ppm->delete($foo);

Searching objects by params
===

To search objects based on parameters, you just need to pass the values to function search, see below:

$ppm = new PPM();

Searching Foo objects that the property bar is equal to 'yumi'
$fooCollection = $ppm->search('Foo', array('bar'=>'yumi') ); //the result is an array of matched objects

Searching Foo object by id
$fooCollection = $ppm->search('Foo', array('id'=>1) ); //the result is an array of matched objects

The parameters accepted by this functions are: $ppm->search( classname, properties/values, pageoffset, quantity, orderby );

Where classname is the name of the Class/Table to search

properties/values is an array where the index represents the attribute name, and the value is the search to match

pageoffset is an integer indicating the number of page in the pagination process

quantity is the number of records to fetch

orderby is an array with the attribute to order as the index and the type "ASC" or "DESC" to order like: array('id'=>'ASC')