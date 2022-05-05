<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Guard.php");
    require_once("controllers/Member.php");
    Guard::member();
    $db = DB::conn();
    if (isset($_POST['submit'])) {
        $member = new Member($db);
        $member->id = $_SESSION['id'];
        $member->username = $_POST['username'];
        $member->firstname = $_POST['firstname'];
        $member->lastname = $_POST['lastname'];
        $member->tel = $_POST['tel'];
        $member->status = $_SESSION['status'];
        $result = $member->update();
        if ($result[0]) {
            $_SESSION['name'] = $_POST['firstname']." ".$_POST['lastname'];
        }
    }
    $member = new Member($db);
    $member->id = $_SESSION['id'];
    $member_result = $member->findOne();
?>
<?php include_once('layouts/header.php'); ?>
<div class="col-lg-12 card">
    <div class="card-body">
        <h3>Profile</h3>
        <form action="profile.php" method="post">
        <div class="row mt-2">
            <div class="col">
                <input required value="<?php echo $member_result[2]['username']; ?>" name="username" type="text"
                       class="form-control" placeholder="username">
            </div>
            <div class="col">
                <input required value="<?php echo $member_result[2]['tel']; ?>" name="tel" type="text"
                       class="form-control" placeholder="tel">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <input required value="<?php echo $member_result[2]['firstname']; ?>" name="firstname"
                       type="text" class="form-control" placeholder="firstname">
            </div>
            <div class="col">
                <input required value="<?php echo $member_result[2]['lastname']; ?>" name="lastname"
                       type="text" class="form-control" placeholder="lastname">
            </div>
        </div>
        <input name="submit" value="Update" type="submit" class="btn btn-primary w-100 mt-3"></input>
        </form>
    </div>
</div>
<?php include_once('layouts/bottom.php'); ?>
