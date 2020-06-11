<?php

/**
 * @file
 * Entity general class.
 */

abstract class Entity {

	protected $id;
	// abstract protected $table;
	// abstract protected $table_fields = [];

	public function get($prop) {
		return $this->$prop;
	}
	public function getArray() {
		$arr = ['id' => $this->id];
		foreach ($this->table_fields as $prop) {
			$arr[$prop] = $this->$prop;
		}
		return $arr;
	}
	public function set($prop, $val) {
		if (in_array($prop, ['id', 'table', 'table_fields'])) {
			throw new LogicException("$prop parameter is locked in " . get_class($this) . ' object');
		}
		if (!isset($this->$prop)) {
			throw new LogicException("$prop parameter is not created in " . get_class($this) . ' object');
		}
		$this->$prop = $val;
	}

	// Create or load entity.
	public function __construct($data) {
		$result = TRUE;

		if (is_array($data)) {
			// Create new object with data array.
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
			$this->is_valid(TRUE);
			$result = $this->create();
		}
		else {
			// Load object by id.
			$this->id = $data;
			$result = $this->read();
			if (!$result) error(get_class($this) . ' ' . $this->id . ' not found.', 0);
		}

		if (!$result) {
			throw new LogicException('Unable to create ' . get_class($this) . ' object');
		}
	}

	// Validate entity.
	protected function is_valid($ignore_id = FALSE) {
		if (!isset($this->id) && !$ignore_id) {
			throw new LogicException(get_class($this) . ' must have id property');
		}
		if (!isset($this->table)) {
			throw new LogicException(get_class($this) . ' must have table property');
		}
		if (!isset($this->table_fields)) {
			throw new LogicException(get_class($this) . ' must have table_fields property');
		}
		foreach ($this->table_fields as $table_field) {
			if (!isset($this->$table_field)) {
				throw new LogicException(get_class($this) . " must have $table_field property");
			}
		}
	}

	// CRUD: Create.
	public function create() {
		global $db;
		$insert_values = array_flip($this->table_fields);
		foreach ($insert_values as $key => $value) {
			$insert_values[$key] = $this->$key;
		}
		$this->id = $db->insert($this->table, $insert_values);
		return TRUE;
	}

	// CRUD: Read.
	public function read() {
		global $db;
		$response = $db->select('*', $this->table, [ ['id', $this->id, '='] ]);
		if (isset($response[0])) {
			foreach ($response[0] as $key => $value) {
				$this->$key = $value;
			}
			return TRUE;
		}
		return FALSE;
	}

	// CRUD: Update.
	public function update() {
		global $db;
		$update_values = array_flip($this->table_fields);
		foreach ($update_values as $key => $value) {
			$update_values[$key] = $this->$key;
		}
		$response = $db->update($this->table, $update_values, [ ['id', $this->id, '='] ] );
		return $response;
	}

	// CRUD: Delete.
	public function delete() {
		global $db;
		$response = $db->delete($this->table, [ ['id', $this->id, '='] ]);
		return $response;
	}
}
