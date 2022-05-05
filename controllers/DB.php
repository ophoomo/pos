<?php
class DB {
    public static function conn() {
        $host = "localhost";
        $user = "root";
        $pass = "1234567890";
        $name = "pos";
        $conn = "";
        try {
            $conn = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $conn;
    }
}
?>