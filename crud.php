<?php
/**
 * Crud class with crud instance functions for the crud operations.
 *   GET:
 *	   - view, list
 *   POST:
 *	   - add
 *   PUT/UPDATE:
 *	   - put, update
 *   DELETE:
 *	   - delete
 */
class Crud {
	
	private $conn;

	function __construct($db) {
		$this->conn = $db;
	}

    public function view($args) {

    }
	
    public function list($args) {

    }

    public function add($args) {

    }
	
    public function update($args) {

    }
	
    public function put($args) {

    }

	public function delete($args) {

	}
}
?>