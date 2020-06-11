<?php

/**
 * @file
 * Database connection layer.
 */

abstract class Database {

	protected $db_host;
	protected $db_database;
	protected $db_username;
	protected $db_password;
	protected $db_port;
	protected $db_query;

	public function __construct($db_host, $db_database, $db_username, $db_password, $db_port) {
		$this->db_host = $db_host;
		$this->db_database = $db_database;
		$this->db_username = $db_username;
		$this->db_password = $db_password;
		$this->db_port = $db_port;
	}

	abstract public function select($fields, $table, $where = 1, $operator = 'AND', $page = FALSE); // returns assoc array.
	abstract public function insert($table, $insert_values); // returns added id or FALSE.
	abstract public function update($table, $set, $where = 1, $operator = 'AND'); // returns count of updated rows.
	abstract public function delete($table, $where = 1, $operator = 'AND'); // returns count of deleted rows.
	abstract public function execute($sql); // returns database raw answer.
	abstract public function close(); // closes database connection.

	// SELECT from multiple tables.
	abstract public function select2($fields, $tables, $where_list, $page = 0, $sort_by = FALSE); // returns assoc array.

	protected $count_page = 10; // default count of rows on every page.
	abstract public function select2_last_page($fields, $tables, $where_list, $page = 0, $sort_by = FALSE); // get last page num.
}
