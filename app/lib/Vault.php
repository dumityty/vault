<?php

/**
 * Install php pdo sqlcipher
 * https://groups.google.com/forum/#!topic/sqlcipher/QntIM-sg_90
 *
 * Otherwise can't use the PRAGMA key
 */

class Vault {

  private $v;
  private $key;
  private $vault_file;

  /**
   * Once more functionality is done replace the masterkey! It will be passed as a parameter.
   *
   * @param string $vault [description]
   * @param string $key   [description]
   */
  public function __construct($vault = "mastervault", $key = "masterkey") {
    $this->vault_file = ROOT_DIR . "/data/" . $vault . ".sqlite";
    if (!file_exists($this->vault_file)) {
      $this->v = $this->vault_install();
    }
    else {
      $this->v = new PDO("sqlite:" . $this->vault_file);
    }

    $this->key = $key;
  }

  private function vault_install() {
    $newvault = new PDO("sqlite:" . $this->vault_file);
    // $newvault->exec("PRAGMA key = 'secretkey'");
    $newvault->exec("CREATE TABLE Credentials (id INTEGER PRIMARY KEY, site TEXT, username TEXT, password TEXT, url TEXT)");
    return $newvault;
  }

  public function addCredential($credential) {
    $sql = "INSERT INTO Credentials (site, username, password, url) VALUES (:site, :username, :password, :url)";
    $query = $this->v->prepare($sql);
    $id = $query->execute(array(
      ':site' => $credential['site'],
      ':username' => $credential['username'],
      ':password' => $this->encrypt($credential['password']),
      ':url' => $credential['url'],
    ));
    krumo($id);
  }

  public function getCredential($id) {
    $sql = "SELECT id,site,username,password,url FROM Credentials WHERE id = :id";
    $stmt = $this->v->prepare($sql);
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $credential = $stmt->fetchObject();

    if (!$credential) {
      return null;
    }
    krumo($credential);
    // decrypt password here?
    $credential->password = $this->decrypt($credential->password);

    return $credential;
  }

  public function getAllCredentials() {
    $sql = "SELECT id,site,username,url FROM Credentials ORDER BY site ASC";
    $stmt = $this->v->query($sql);
    $credentials = $stmt->fetchAll(PDO::FETCH_OBJ);

    // decrypt password here?

    return $credentials;
  }

  private function encrypt($string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = $this->key;
    $secret_iv = 'IV';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);

    return $output;
  }

  private function decrypt($string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = $this->key;
    $secret_iv = 'IV';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);

    return $output;
  }

}
