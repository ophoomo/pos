<?php
require_once ("controllers/Encryption.php");
class Auth {

    public $username;
    public $password;

    private $db;
    function __construct($conn) {
        $this->db = $conn;
    }

    public function Login() {
        $check_email = "SELECT * FROM tb_members WHERE username = ? LIMIT 1";
        $query = $this->db->prepare($check_email);
        $query->bindParam(1, $this->username);
        $execute = $query->execute();
        if (!$execute) {
            return [false, "เกิดข้อผิดพลาดจากเซิฟเวอร์"];
        }

        if ($query->rowCount() == 0) {
            return [false, "ไม่พบอีเมลนี้"];
        }

        $data = $query->fetch(PDO::FETCH_ASSOC);

        $encryption = new Encryption();
        $encryption->set_salt($data['salt']);
        $password_encode = $encryption->get_password($this->password);
        if ($password_encode !== $data['password']) {
            return [false, "รหัสผ่านไม่ถูกต้อง"];
        }

        return [true, "เข้าสู่ระบบสำเร็จ", $data];
    }

}
?>