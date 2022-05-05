<?php
class Order {

    public $id;
    public $memberID;
    public $received;
    public $total;

    private $db;
    private $table = "tb_orders";

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function find() {
        $data = [];
        $sql = "SELECT $this->table.id, $this->table.total, $this->table.received, tb_customers.firstname, tb_customers.lastname FROM $this->table LEFT JOIN tb_customers ON $this->table.member_id = tb_customers.id";
		
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

    public function findLast() {
        $sql = "SELECT * FROM $this->table ORDER BY id DESC LIMIT 1";
        $query = $this->db->prepare($sql);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return [true, "ดึงข้อมูลเสร็จสิ้น", $data];
    }

    public function create() {
        $sql = "INSERT INTO $this->table (member_id , received, total) VALUES (?, ?, ?)";
        $query = $this->db->prepare($sql);
        if (!empty($this->memberID)) {
            $query->bindParam(1, $this->memberID, PDO::PARAM_INT);
        } else {
            $query->bindParam(1, $this->memberID, PDO::PARAM_NULL);
        }
        $query->bindParam(2, $this->received, PDO::PARAM_INT);
        $query->bindParam(3, $this->total, PDO::PARAM_INT);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }
        return [true, "เพิ่มรายการสั่งซื้อเสร็จสิ้น"];
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
