<?php

/**
*	todo:
* The database install needs better management (use the DbInstall thing)
*/

class User {
	
	public $id;
	public $email;
	private $key;
	private $udb;
	private $users_file;

	public function __construct() {
		$this->users_file = ROOT_DIR . "/data/users.sqlite";
		
		if (!file_exists($this->users_file)) {
      $this->udb = $this->users_install();
    }
    else {
      $this->udb = new PDO("sqlite:" . $this->users_file);
    }
	}

	private function users_install() {
    $newudb = new PDO("sqlite:" . $this->users_file);
    $newudb->exec("CREATE TABLE Users (id INTEGER PRIMARY KEY, email TEXT, key TEXT)");
    return $newudb;
  }

	public function login($data) {
		krumo($this->udb);
		$sql = "SELECT id,email,key FROM Users WHERE email = :email AND password = :password";
    $stmt = $this->udb->prepare($sql);
    krumo($stmt);
    $stmt->bindParam("email", $data['email']);
    $stmt->bindParam("password", hash('SHA512',$data['key']));
    $stmt->execute();
    $user = $stmt->fetchObject();

    krumo($user);
    if (!$user) {
      return false;
    }

    $this->id = $user->id;
    $this->email = $user->email;
    $this->key = $data['key'];
    return true;
	}

}