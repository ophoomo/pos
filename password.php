<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Guard.php");
    require_once("controllers/Member.php");
    require_once("controllers/Encryption.php");
    Guard::member();
    $db = DB::conn();
    $result = null;
    if (isset($_POST['submit'])) {
        if ($_POST['new_password'] == $_POST['confirm_password']) {
            $member = new Member($db);
            $member->id = $_SESSION['id'];
            $member_result = $member->findOne();
            $encryption = new Encryption();
            $encryption->set_salt($member_result[2]['salt']);
            $old = $encryption->get_password($_POST['old_password']);
            if ($old == $member_result[2]['password']) {
                $member->password = $_POST['confirm_password'];
                $result = $member->changePassword();
            } else {
                $result = [false, "รหัสผ่านเดิมไม่ถูกต้อง"];
            }
        } else {
            $result = [false, "รหัสผ่านไม่ตรงกัน"];
        }
    }
?>
<?php include_once('layouts/header.php'); ?>
<form action="password.php" method="post">
<div class="col-lg-12 card">
    <div class="card-body">
        <h3>Change Password</h3>
        <div class="row mt-2">
            <div class="col">
                <input name="old_password" type="text" class="form-control" required placeholder="old password">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <input name="new_password" type="text" class="form-control" required placeholder="new password">
            </div>
            <div class="col">
                <input name="confirm_password" type="text" class="form-control" required placeholder="confirm password">
            </div>
        </div>
        <input name="submit" type="submit" value="Update" class="btn btn-primary w-100 mt-3"></input>
    </div>
</div>
</form>
<?php include_once('layouts/bottom.php'); ?>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if(!is_null($result)): ?>
    Swal.fire({
        icon: '<?php echo $result[0] ? 'success' : 'error'; ?>',
        title: '<?php echo $result[0] ? 'สำเร็จ' : 'เกิดข้อผิดพลาด'; ?>',
        text: '<?php echo $result[1]; ?>',
    });
    <?php endif; ?>
</script>
