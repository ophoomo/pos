<?php
class Guard {
    public static function member() {
        if(!isset($_SESSION['id'])) {
            header("Location: login.php");
        }
    }
    public static function manager() {
        if(isset($_SESSION['status']) && $_SESSION['status'] !== 'manager') {
            header("Location: index.php");
        }
    }
}
?>