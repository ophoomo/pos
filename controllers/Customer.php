<?php
class Customer {

    public $id;
    public $firstname;
    public $lastname;
    public $tel;

    private $db;
    private $table = "tb_customers";

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function find() {
        $data = [];
        $sql = "SELECT * FROM $this->table";
        $query = $this->db->prepare($sql);
        $query->execute();
        while($item = $query->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $item;
        }
        return [true, "ดึงข้อมูลเสร็จสิ้น", $data];
    }

    public function findOne() {
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->id, PDO::PARAM_STR);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return [true, "ดึงข้อมูลเสร็จสิ้น", $data];
    }

    public function create() {
        if (!isset($this->firstname) && !isset($this->lastname) && !isset($this->tel)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        if ($this->check_tel()) {
            return [false, "เบอร์โทรซ้ำ"];
        }
        $sql = "INSERT INTO $this->table (firstname, lastname, tel) VALUES (?, ?, ?)";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->firstname, PDO::PARAM_STR);
        $query->bindParam(2, $this->lastname, PDO::PARAM_STR);
        $query->bindParam(3, $this->tel, PDO::PARAM_STR);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เพิ่มข้อมูลลูกค้าเสร็จสิ้น"];
    }

    public function update() {
        if (!isset($this->firstname) && !isset($this->lastname) && !isset($this->tel) &&  !isset($this->id)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        if ($this->check_tel(true)) {
            return [false, "เบอร์โทรซ้ำ"];
        }
        $sql = "UPDATE $this->table SET firstname = ?, lastname = ?, tel = ? WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->firstname, PDO::PARAM_STR);
        $query->bindParam(2, $this->lastname, PDO::PARAM_STR);
        $query->bindParam(3, $this->tel, PDO::PARAM_STR);
        $query->bindParam(4, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "แก้ไขข้อมูลลูกค้าเสร็จสิ้น"];
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
        return [true, "ลบข้อมูลลูกค้าเสร็จสิ้น"];
    }

    private function check_tel($status = false) {
        if ($status) {
            $sql = "SELECT tel FROM $this->table WHERE tel = ? AND id != ?";
            $query = $this->db->prepare($sql);
            $query->bindParam(1, $this->tel, PDO::PARAM_STR);
            $query->bindParam(2, $this->id, PDO::PARAM_INT);
        } else {
            $sql = "SELECT tel FROM $this->table WHERE tel = ?";
            $query = $this->db->prepare($sql);
            $query->bindParam(1, $this->tel, PDO::PARAM_STR);
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
