<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Guard.php");
    require_once("controllers/Customer.php");
    Guard::member();
    $db = DB::conn();
    $result = [];
    if(isset($_GET['id']) && isset($_GET['status'])) {
        $status = $_GET['status'];
        if($status === "remove") {
            $customer = new Customer($db);
            $customer->id = $_GET['id'];
            $result = $customer->delete();

        } else if ($status === "update") {
            $customer = new Customer($db);
            $customer->id = $_GET['id'];
            $customer->firstname = $_POST['firstname'];
            $customer->lastname = $_POST['lastname'];
            $customer->tel = $_POST['tel'];
            $result = $customer->update();
        }
    } else if(isset($_GET['status']) && $_GET['status'] === 'add') {
        $customer = new Customer($db);
        $customer->firstname = $_POST['firstname'];
        $customer->lastname = $_POST['lastname'];
        $customer->tel = $_POST['tel'];
        $result = $customer->create();
    } else if (isset($_GET['id'])) {
        $customer = new Customer($db);
        $customer->id = $_GET['id'];
        $customer_findOne_result = $customer->findOne();
    }

    $customer = new Customer($db);
    $customer_result = $customer->find();
?>
<?php include_once('layouts/header.php'); ?>


    <div class="col-lg-12 card">
        <div class="card-body">
            <button class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModal">ADD</button>
            <table id="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Tel</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($customer_result[2] as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['firstname']; ?></td>
                    <td><?php echo $item['lastname']; ?></td>
                    <td><?php echo $item['tel']; ?></td>
                    <td>
                        <a href="customer.php?id=<?php echo $item['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-fw fa-edit"></i></a>
                        </a>
                        <a href="customer.php?id=<?php echo $item['id']; ?>&status=remove" class="btn btn-danger">
                            <i class="fas fa-fw fa-trash"></i></a>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add" action="customer.php?status=add" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <input name="firstname" type="text" class="form-control" placeholder="Firstname">
                        </div>
                        <div class="col">
                            <input name="lastname" type="text" class="form-control" placeholder="Lastname">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input name="tel" type="text" class="form-control" placeholder="Tel">
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
    <?php if(isset($customer_findOne_result)): ?>
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Edit Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add" action="customer.php?id=<?php echo $customer_findOne_result[2]['id']; ?>&status=update"
                      method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <input value="<?php echo $customer_findOne_result[2]['firstname']; ?>" name="firstname"
                                       type="text" class="form-control" placeholder="Firstname">
                            </div>
                            <div class="col">
                                <input value="<?php echo $customer_findOne_result[2]['lastname']; ?>" name="lastname"
                                       type="text" class="form-control" placeholder="Lastname">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                <input name="tel" type="text" class="form-control"
                                       value="<?php echo $customer_findOne_result[2]['tel']; ?>" placeholder="Tel">
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
        <?php if (isset($customer_findOne_result)): ?>
            $('#edit_modal').modal('show');
        <?php endif; ?>
    } );
</script>
