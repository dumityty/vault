<?php

/**
 * Install php pdo sqlcipher
 * https://groups.google.com/forum/#!topic/sqlcipher/QntIM-sg_90
 *
 * Otherwise can't use the PRAGMA key
 */

class User {

  public $id;
	public $email;
  private $udb;
  private $users_file;

/**
   * Once more functionality is done replace the masterkey! It will be passed as a parameter.
   *
   * @param string $vault [description]
   * @param string $key   [description]
   */
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
    // $newvault->exec("PRAGMA key = 'secretkey'");
    $newudb->exec("CREATE TABLE Users (id INTEGER PRIMARY KEY, email TEXT, key TEXT)");
    return $newudb;
  }

  public function login($data) {
    krumo($data);
    $sql = "SELECT id, email, key FROM Users WHERE email = :email AND key = :key";
    $stmt = $this->udb->prepare($sql);
    $stmt->bindParam("email", $data['email']);
    $stmt->bindParam("key", hash('SHA512',$data['key']));
    $stmt->execute();
    $user = $stmt->fetchObject();

    if (!$user) {
      return false;
    }

    $this->id = $user->id;
    $this->email = $user->email;
    $this->key = $data['key'];

    return true;
  }

  public function add($user) {
    $sql = "INSERT INTO Users (email, key) VALUES (:email, :key)";
    $stmt = $this->udb->prepare($sql);
    $stmt->bindParam('email', $user['email']);
    $stmt->bindParam('key', hash('SHA512',$user['key']));

    $stmt->execute();
    $id = $this->udb->lastInsertId();
    return $id;
  }

  public function getKey($vault = "master") {
    return "masterkey2";
  }

 }