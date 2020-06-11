<?php

require_once 'Entity.php';

/**
 * @file
 * Entity: User.
 */

class User extends Entity {

	protected $table = 'users';
	protected $table_fields = ['email', 'first_name', 'last_name', 'password_md5', 'status', 'obj'];

	protected $email;
	protected $first_name;
	protected $last_name;
	protected $password_md5;
	protected $status;
	protected $obj;

	protected $company;

	// $data is either ['email', 'first_name', 'last_name', 'password', 'status'] or INT id.
	public function __construct($data) {
		// Modify $data to $table_fields view.
		if (is_array($data)) {
			$data['email'] = strtolower($data['email']);
			$data['password'] = '123';
			$data['status'] = '1';
			$data['password_md5'] = md5($data['password']); unset($data['password']);

			$data['obj'] = [];
			if (isset($data['company'])) $data['obj']['company'] = $data['company'];

			// Check.
			$this->email = $data['email'];
			$result = $this->read();
			if ($result) error(get_class($this) . ' ' . $this->email . ' aleady existed.', 0);

			parent::__construct($data);
		}
		else {
			// Load object by email.
			$this->email = $data;
			$result = $this->read();
			if (!$result) error(get_class($this) . ' ' . $this->email . ' not found.', 0);
		}
	}

	// CRUD: Read.
	public function read() {
		global $db;
		$response = $db->select('*', $this->table, [ ['email', $this->email, '='] ]);
		if (isset($response[0])) {
			foreach ($response[0] as $key => $value) {
				$this->$key = $value;
			}

			$this->obj = json_decode($this->obj);
			foreach ($this->obj as $key => $value) {
				$this->$key = $value;
			}

			return TRUE;
		}
		return FALSE;
	}

	public function getArray() {
		$arr = parent::getArray();
		unset($arr['password_md5']);
		foreach ($arr['obj'] as $key => $value) {
			$arr[$key] = $value;
		}
		unset($arr['obj']);
		return $arr;
	}

	// Login and get user object.
	// ['login', 'password']
	static public function login($params) {
		global $db;

		$login = strtolower($params['login']);
		$result = $db->select(['id'], 'users', [
			['email', $login, '='],
			['password_md5', md5($params['password']), '='],
			['status', '0', '!=']
		]);
		if (count($result)) {
			$user = new User($result[0]['id']);
			if ($user->get('status') == '2') $user->setStatus('1');
			return $user;
		}

		return FALSE;
	}

	// Register and get user object.
	// ['email', 'first_name', 'last_name', 'password']
	static public function register($params) {
		$params['email'] = strtolower($params['email']);
		$params['status'] = 3; // Email is not approved yet.

		$user = new User($params);
		if ($user) {
			return $user;
		}

		return FALSE;
	}

	// Invite new user and get his password automatically generated.
	// ['email', 'first_name', 'last_name', 'password']
	static public function invite($params) {
		$params['email'] = strtolower($params['email']);
		$params['status'] = 2; // User is not approved yet.

		$user = new User($params);
		if ($user) {
			return $user;
		}

		return FALSE;
	}

	// Generate new password.
	static public function randomPassword() {
    	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    	$pass = [];
    	$alphaLength = strlen($alphabet) - 1;
    	for ($i = 0; $i < 8; $i++) {
    		$n = rand(0, $alphaLength);
    		$pass[] = $alphabet[$n];
    	}
    	return implode($pass);
    }



    /**
     * Attach user statuses to this class.
     */
    public function setStatus($status = 1) {
    	// 0 - Blocked
    	// 1 - Active
    	// 2 - Invited
    	// 3 - Email is not approved yet
    	$this->status = $status;
    	$this->update();
    }

}
