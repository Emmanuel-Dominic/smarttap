<?php
/**
 * Database class which carries the connect instance function. 
 */
class DB {
    private $db_host = 'localhost';
    private $db_name = 'smarttap';
    private $db_user = 'root';
    protected $db_pass = '';
    private $conn;

    public function connect() {
    	$this->conn=null;
        try {
	    	$this->conn = new PDO('mysql:host=' . $this->db_host . ';dbname=' . $this->db_name . ';charset=utf8', $this->db_user, $this->db_pass);
	        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    } catch (PDOException $exception) {
	    	exit('Failed to connect to database!');
	    }
	    return $this->conn;
    }
}
?>