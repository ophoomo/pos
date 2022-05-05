<?php
class Product {

    public $id;
    public $name;
    public $price;
    public $discount;
    public $stock;
    public $image;

    private $db;
    private $table = "tb_products";

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
            $data[] = $item;
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
        if (!isset($this->name) && !isset($this->price) && !isset($this->discount) && !isset($this->stock) &&
            !isset($this->image)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        if ($this->check_name_product()) {
            return [false, "ชื่อสินค้าซ้ำ"];
        }
        $sql = "INSERT INTO $this->table (name, price, discount, stock, image) VALUES (?,?,?,?,?)";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->name, PDO::PARAM_STR);
        $query->bindParam(2, $this->price, PDO::PARAM_INT);
        $query->bindParam(3, $this->discount, PDO::PARAM_STR);
        $query->bindParam(4, $this->stock, PDO::PARAM_INT);
        $query->bindParam(5, $this->image, PDO::PARAM_STR);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เพิ่มสินค้าเสร็จสิ้น"];
    }

    public function update() {
        if (!isset($this->name) && !isset($this->price) && !isset($this->discount) && !isset($this->stock) &&
            !isset($this->id)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        if ($this->check_name_product(true)) {
            return [false, "ชื่อสินค้าซ้ำ"];
        }
        $sql = "UPDATE $this->table SET name = ?, price = ?, discount = ?, stock = ? WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->name, PDO::PARAM_STR);
        $query->bindParam(2, $this->price, PDO::PARAM_INT);
        $query->bindParam(3, $this->discount, PDO::PARAM_STR);
        $query->bindParam(4, $this->stock, PDO::PARAM_INT);
        $query->bindParam(5, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if(!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "แก้ไขข้อมูลเสร็จสิ้น"];
    }

    public function UpdateStock($qty, $status = false) {
        if ($status) {
            $sql = "UPDATE $this->table SET stock = stock + ? WHERE id = ?";
        } else {
            $sql = "UPDATE $this->table SET stock = stock - ? WHERE id = ?";
        }
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $qty, PDO::PARAM_INT);
        $query->bindParam(2, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if(!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "แก้ไขข้อมูลเสร็จสิ้น"];
    }

    public function changeImage() {
        if (!isset($this->image) && !isset($this->id)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        $sql = "UPDATE $this->table SET image = ? WHERE id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->image, PDO::PARAM_STR);
        $query->bindParam(2, $this->id, PDO::PARAM_INT);
        $execute = $query->execute();
        if(!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เปลื่ยนรูปภาพเสร็จสิ้น"];
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
        return [true, "ลบสินค้าเสร็จสิ้น"];
    }

    private function check_name_product($status = false) {
        if ($status) {
            $sql = "SELECT name FROM $this->table WHERE name = ? AND id != ?";
            $query = $this->db->prepare($sql);
            $query->bindParam(1, $this->name, PDO::PARAM_STR);
            $query->bindParam(2, $this->id, PDO::PARAM_INT);
        } else {
            $sql = "SELECT name FROM $this->table WHERE name = ?";
            $query = $this->db->prepare($sql);
            $query->bindParam(1, $this->name, PDO::PARAM_STR);
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
