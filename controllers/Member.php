<?php
require_once("controllers/Encryption.php");
class Member {

    public $id;
    public $username;
    public $password;
    public $firstname;
    public $lastname;
    public $tel;
    public $status;

    private $db;
    private $salt;
    private $table = "tb_members";

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function find() {
        $data = [];
        $sql = "SELECT * FROM $this->table";
        $query = $this->db->prepare($sql);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        while($item = $query->fetch(PDO::FETCH_ASSOC)) {
            array_push($data, $item);
        }
        return [true, "ดึงข้อมูลเสร็จสิ้น", $data];
    }

    public function findOne() {
        $sql = "SELECT * FROM $this->table WHERE id = ? LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return [true, "ดึงข้อมูลเสร็จสิ้น", $data];
    }

    public function create() {
        if (!isset($this->username) && !isset($this->password) && !isset($this->firstname) && !isset($this->lastname) &&
            !isset($this->tel) && !isset($this->status)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        if ($this->check_username()) {
            return [false, "ชื่อผู้ใช้งานซ้ำ"];
        }
        $encryption = new Encryption();
        $this->salt = $encryption->get_salt();
        $passwordEncode = $encryption->get_password($this->password);
        $sql = "INSERT INTO $this->table (username, password, salt, firstname, lastname, tel, status) VALUES
                                                                                      (?,?,?,?,?,?,?) ";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->username, PDO::PARAM_STR);
        $query->bindParam(2, $passwordEncode, PDO::PARAM_STR);
        $query->bindParam(3, $this->salt, PDO::PARAM_STR);
        $query->bindParam(4, $this->firstname, PDO::PARAM_STR);
        $query->bindParam(5, $this->lastname, PDO::PARAM_STR);
        $query->bindParam(6, $this->tel, PDO::PARAM_STR);
        $query->bindParam(7, $this->status, PDO::PARAM_STR);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เพิ่มสมาชิกเสร็จสิ้น"];
    }

    public function update() {
        if (!isset($this->username) && !isset($this->id) && !isset($this->firstname) && !isset($this->lastname) &&
            !isset($this->tel) && !isset($this->status)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        if ($this->check_username(true)) {
            return [false, "ชื่อผู้ใช้งานซ้ำ"];
        }
        $sql = "UPDATE $this->table SET username = ?, firstname = ?, lastname = ?, tel = ?, status = ? WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->username, PDO::PARAM_STR);
        $query->bindParam(2, $this->firstname, PDO::PARAM_STR);
        $query->bindParam(3, $this->lastname, PDO::PARAM_STR);
        $query->bindParam(4, $this->tel, PDO::PARAM_STR);
        $query->bindParam(5, $this->status, PDO::PARAM_STR);
        $query->bindParam(6, $this->id, PDO::PARAM_STR);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "แก้ไขข้อมูลเสร็จสิ้น"];
    }

    public function changePassword() {
        if (!isset($this->id)) {
            return [false, "ไม่มี ID"];
        }
        if (!isset($this->password)) {
            return [false, "ไม่มี Password"];
        }
        $encryption = new Encryption();
        $this->salt = $encryption->get_salt();
        $passwordEncode = $encryption->get_password($this->password);
        $sql = "UPDATE $this->table SET salt = ?, password = ? WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->salt, PDO::PARAM_STR);
        $query->bindParam(2, $passwordEncode, PDO::PARAM_STR);
        $query->bindParam(3, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เปลื่ยนรหัสผ่านเสร็จสิ้น"];
    }

    public function delete() {
        if (!isset($this->id)) {
            return [false, "ไม่มี ID"];
        }
        $sql = "DELETE FROM $this->table WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if(!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "ลบสมาชิกเสร็จสิ้น"];
    }

    private function check_username($status = false) {
        if ($status) {
            $sql = "SELECT username FROM $this->table WHERE username = ? AND id != ?";
            $query = $this->db->prepare($sql);
            $query->bindParam(1, $this->username, PDO::PARAM_STR);
            $query->bindParam(2, $this->id, PDO::PARAM_INT);
        } else {
            $sql = "SELECT username FROM $this->table WHERE username = ?";
            $query = $this->db->prepare($sql);
            $query->bindParam(1, $this->username, PDO::PARAM_STR);
        }
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        if ($query->rowCount() > 0) {
            return true;
        }
        return false;
    }

}
?>
