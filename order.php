<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Order.php");
    require_once("controllers/DetailOrder.php");
    require_once("controllers/Guard.php");
    require_once("controllers/Product.php");
    Guard::member();
    $db = DB::conn();
    $result = [];
    if(isset($_GET['id']) && isset($_GET['status'])) {
        $status = $_GET['status'];
        if($status === "remove") {
            $detailOrder = new DetailOrder($db);
            $detailOrder->orderID = $_GET['id'];
            $detailOrder_result = $detailOrder->findByOrderID();

            foreach ($detailOrder_result[2] as $item) {
                $product = new Product($db);
                $product->id = $item['product_id'];
                $product->UpdateStock($item['qty'], true);
            }

            $order = new Order($db);
            $order->id = $_GET['id'];
            $order->delete();
        }
    }

    $order = new Order($db);
    $order_result =  $order->find();
?>
<?php include_once('layouts/header.php'); ?>
<div class="col-lg-12 card">
    <div class="card-body">
        <a class="btn btn-success mb-3" href="index.php">ADD</a>
        <table id="table">
            <thead>
                <tr>
                    <th>OrderID</th>
					<th>Name</th>
                    <th>Received</th>
                    <th>Total</th>
					<th>Detail</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($order_result[2] as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
					<td><?php echo $item['firstname']; ?> <?php echo $item['lastname']; ?></td>
                    <td><?php echo $item['received']; ?></td>
                    <td><?php echo $item['total']; ?></td>
                    <td><button class="btn btn-primary" onclick="window.open('show_detail.php?id=<?php echo $item['id'];
                    ?>', '','width=600,height=600')">
                            <i class="fas fa-fw fa-list-ul"></i></a>
                        </button></td>
                    <td>
                        <a href="order.php?id=<?php echo $item['id'] ?>&status=remove" class="btn btn-danger">
                            <i class="fas fa-fw fa-trash"></i></a>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once('layouts/bottom.php'); ?>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script>
    $(document).ready( function () {
        $('#table').DataTable();
    } );
</script>
