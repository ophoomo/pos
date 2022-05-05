<?php
class DetailOrder {
    public $id;
    public $orderID;
    public $productID;
    public $qty;
    public $price;

    private $db;
    private $table = "tb_detail_orders";

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function findByOrderID() {
        $data = [];
        $sql = "SELECT * FROM $this->table WHERE order_id = ?";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->orderID, PDO::PARAM_INT);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        while($item = $query->fetch(PDO::FETCH_ASSOC)) {
            array_push($data, $item);
        }
        return [true, "ดึงข้อมูลเสร็จสิ้น", $data];
    }

    public function create() {
        $sql = "INSERT INTO $this->table (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)";
        $query = $this->db->prepare($sql);
        $query->bindParam(1, $this->orderID, PDO::PARAM_INT);
        $query->bindParam(2, $this->productID, PDO::PARAM_INT);
        $query->bindParam(3, $this->qty, PDO::PARAM_INT);
        $query->bindParam(4, $this->price, PDO::PARAM_INT);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เพิ่มรายละเอียดสินค้าเสร็จสิ้น"];
    }

    public function update() {
        if (!isset($this->id)) {
            return [false, "ข้อมูลไม่ครบถ้วน"];
        }
        $sql = "";
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
        return [true, "ลบเสร็จสิ้น"];
    }

}
?>