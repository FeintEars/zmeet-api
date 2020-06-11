<?php

require_once 'Database.php';

/**
 * @file
 * MySQL connector.
 */

class MySQL extends Database {

	protected $link;

	public function __construct($db_host, $db_database, $db_username, $db_password, $db_port = 3306) {
		parent::__construct($db_host, $db_database, $db_username, $db_password, $db_port);
		$this->link = new mysqli($db_host, $db_username, $db_password, $db_database, $db_port);
	}

	public function select($fields, $table, $where_list = 1, $operator = 'AND', $page = FALSE) {
		$what = $fields;
		if (is_array($fields)) {
			foreach ($fields as $key => $field) {
				$fields[$key] = $table . '.' . $field;
			}
			$what = implode($fields, ', ');
		}
		$where = $this->where($table, $where_list);

		// Add the $page support here.

		$sql = "SELECT $what FROM `$table` WHERE $where;";

		$response = $this->execute($sql);
		if ($response) return $response->fetch_all(MYSQLI_ASSOC);
		return [];
	}

	public function insert($table, $insert_values) {
		$into = [];
		$values = [];
		foreach ($insert_values as $key => $value) {
			$into[] = '`' . $key . '`';
			$values[] = '"' . $this->link->real_escape_string($value) . '"';
		}
		$into = implode($into, ', ');
		$values = implode($values, ', ');
		$sql = "INSERT INTO `$table`($into) VALUES($values);";
		$response = $this->execute($sql);

		if ($response) {
			$result = $this->execute("SELECT MAX(`$table`.`id`) FROM `$table` WHERE 1;");
			$response = $result->fetch_all(MYSQLI_ASSOC);
			return current($response[0]);
		}

		return FALSE;
	}

	public function update($table, $update_values, $where = 1, $operator = 'AND') {
		$set = [];
		foreach ($update_values as $key => $value) {
			$set[] = '`' . $key . '` = "' . $this->link->real_escape_string($value) . '"';
		}
		$set = implode($set, ', ');
		$where = $this->where($table, $where, $operator);
		$sql = "UPDATE `$table` SET $set WHERE $where;";

		$response = $this->execute($sql);
		return $response;
	}

	public function delete($table, $where = 1, $operator = 'AND') {
		$where = $this->where($table, $where, $operator);
		$sql = "DELETE FROM `$table` WHERE $where;";

		$response = $this->execute($sql);
		return $response;
	}

	public function execute($sql) {
		return $this->link->query($sql);
	}

	public function close() {
		$this->link->close();
	}

	protected function where($table, $where_list, $operator = 'AND') {
		if (!is_array($where_list)) return $where_list;
		$where = [];
		foreach ($where_list as $key => $value) {
			$where[] = '`' . $table . '`.`' . $value[0] . '`' . $value[2] . '"' . $value[1] . '"';
		}
		$where = implode($where, " $operator ");
		return $where;
	}

	/**
	 * Select from multiple tables.
	 */
	public function select2($fields, $tables, $where_list, $page = 0, $sort_by = FALSE) {
		$what = $fields;
		if (is_array($fields)) {
			$what = implode($fields, ', ');
		}

		$tables = implode($tables, ', ');

		if (!is_array($where_list)) $where = $where_list;
		else {
		$where = [];
			foreach ($where_list as $key => $value) {
				$where[] = $value[0] . $value[2] . $value[1];
			}
			$where = implode($where, ' AND ');
		}

		$sql = "SELECT $what FROM $tables WHERE $where";
		if ($sort_by) $sql .= " ORDER BY $sort_by";
		$limit = $page * $this->count_page; $sql .= " LIMIT $limit, " . $this->count_page;
		$sql .= ';';

		$response = $this->execute($sql);
		if ($response) return $response->fetch_all(MYSQLI_ASSOC);
		return [];
	}

	public function select2_last_page($fields, $tables, $where_list, $page = 0, $sort_by = FALSE) {
		$result = $this->select2($fields, $tables, $where_list, $page = 0, $sort_by = FALSE);
		if (count($result) == 1) {
			if (count(current($result)) == 1) {
				$count = intval((current(current($result)) - 1) / $this->count_page) + 1;
				return $count;
			}
		}
		return FALSE;
	}

}
