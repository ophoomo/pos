<?php
class Encryption {
    private $salt;

    function __construct() {
        $this->salt = $this->generatorSalt();
    }

    public function get_salt() {
        return $this->salt;
    }

    public function set_salt($salt) {
        $this->salt = $salt;
    }

    public function get_password($password) {
        return hash_hmac("sha256", $password, $this->salt);
    }

    private function generatorSalt() {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/\\][{}\'";:?.>,<!@#$%^&*()-_=+|';
        $randStringLen = rand(30,40);

        $randString = "";
        for ($i = 0; $i < $randStringLen; $i++) {
            $randString .= $charset[mt_rand(0, strlen($charset) - 1)];
        }

        return $randString;
    }

}
?>
