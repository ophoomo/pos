<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Member.php");
    require_once("controllers/Guard.php");
    Guard::member();
    Guard::manager();
    $db = DB::conn();
    $result = [];
    if(isset($_GET['id']) && isset($_GET['status'])) {
        $status = $_GET['status'];
        if($status === "remove") {
            $member = new Member($db);
            $member->id = $_GET['id'];
            $result = $member->delete();
        } else if ($status === "update") {
            $member = new Member($db);
            $member->id = $_GET['id'];
            $member->username = $_POST['username'];
            $member->firstname = $_POST['firstname'];
            $member->lastname = $_POST['lastname'];
            $member->tel = $_POST['tel'];
            $member->status = $_POST['status'];
            $result = $member->update();
        } else if ($status === "password") {
            $member = new Member($db);
            $member->id = $_GET['id'];
            $member->password = $_POST['new_password'];
            $result = $member->changePassword();
        }
    } else if(isset($_GET['status']) && $_GET['status'] === 'add') {
        $member = new Member($db);
        $member->username = $_POST['username'];
        $member->password = $_POST['password'];
        $member->firstname = $_POST['firstname'];
        $member->lastname = $_POST['lastname'];
        $member->tel = $_POST['tel'];
        $member->status = $_POST['status'];
        $result = $member->create();
    } else if (isset($_GET['id'])) {
        $member = new Member($db);
        $member->id = $_GET['id'];
        $member_findOne_result = $member->findOne();
    }

    $member = new Member($db);
    $member_result = $member->find();
?>
<?php include_once('layouts/header.php'); ?>
<div class="col-lg-12 card">
    <div class="card-body">
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModal">ADD</button>
        <table id="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Tel</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($member_result[2] as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['username']; ?></td>
                    <td><?php echo $item['firstname']; ?></td>
                    <td><?php echo $item['lastname']; ?></td>
                    <td><?php echo $item['tel']; ?></td>
                    <td>
                        <a href="member.php?id=<?php echo $item['id']; ?>&status=change_password" class="btn btn-info">
                            <i class="fas fa-fw fa-lock"></i></a>
                        </a>
                        <a href="member.php?id=<?php echo $item['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-fw fa-edit"></i></a>
                        </a>
                        <a href="member.php?id=<?php echo $item['id']; ?>&status=remove" class="btn btn-danger">
                            <i class="fas fa-fw fa-trash"></i></a>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add" method="post" action="member.php?status=add">
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <input required name="username" type="text" class="form-control" placeholder="Username">
                    </div>
                    <div class="col">
                        <input required name="password" type="text" class="form-control" placeholder="Password">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <input required name="firstname" type="text" class="form-control" placeholder="Firstname">
                    </div>
                    <div class="col">
                        <input required name="lastname" type="text" class="form-control" placeholder="Lastname">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <input required name="tel" type="text" class="form-control" placeholder="Tel">
                    </div>
                    <div class="col">
                        <select class="form-control" name="status" required>
                            <option value="employee">พนักงาน</option>
                            <option value="manager">หัวหน้า</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">ADD</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php if(isset($member_findOne_result)): ?>
<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_modal_label">Edit Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit" method="post" action="member.php?id=<?php echo $member_findOne_result[2]['id']; ?>&status=update">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <input value="<?php echo $member_findOne_result[2]['username']; ?>"
                                   required name="username" type="text" class="form-control" placeholder="Username">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input value="<?php echo $member_findOne_result[2]['firstname']; ?>"
                                   required name="firstname" type="text" class="form-control" placeholder="Firstname">
                        </div>
                        <div class="col">
                            <input value="<?php echo $member_findOne_result[2]['lastname']; ?>"
                                   required name="lastname" type="text" class="form-control" placeholder="Lastname">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input value="<?php echo $member_findOne_result[2]['tel']; ?>"
                                   required name="tel" type="text" class="form-control" placeholder="Tel">
                        </div>
                        <div class="col">
                            <select class="form-control" name="status" required>
                                <option <?php echo $member_findOne_result[2]['status'] === 'employee' ? 'selected' : '' ?>
                                        value="employee">พนักงาน</option>
                                <option <?php echo $member_findOne_result[2]['status'] === 'manager' ? 'selected' : '' ?>
                                        value="manager">หัวหน้า</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if(isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] === 'change_password'): ?>
<div class="modal fade" id="password_modal" tabindex="-1" role="dialog" aria-labelledby="password_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="password_modal_label">Change Password Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="password" method="post" action="member.php?id=<?php echo $_GET['id']; ?>&status=password">
                <div class="modal-body">
                    <div class="row mt-2">
                        <div class="col">
                            <input required name="new_password" type="text" class="form-control" placeholder="new password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php include_once('layouts/bottom.php'); ?>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script>
    $(document).ready( function () {
        $('#table').DataTable();
        <?php if(isset($member_findOne_result)): ?>
            $('#edit_modal').modal('show');
        <?php endif; ?>
        <?php if(isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] === 'change_password'): ?>
        $('#password_modal').modal('show');
        <?php endif; ?>
    } );
</script>
