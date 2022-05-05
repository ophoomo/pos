<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Product.php");
    require_once("controllers/DetailOrder.php");
    require_once("controllers/Guard.php");
    Guard::member();
    $db = DB::conn();
    if (isset($_GET['id'])) {
        $detailOrder = new DetailOrder($db);
        $detailOrder->orderID = $_GET['id'];
        $detailOrder_result = $detailOrder->findByOrderID();
    }

    $total = 0;
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Detail Order</title>
</head>
<body>

<div class="p-5">
    <table class="table w-100">
        <thead>
        <tr>
            <th>ID</th>
            <th>ProductName</th>
            <th>Qty</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (isset($detailOrder_result)):
            foreach ($detailOrder_result[2] as $index => $item):
                $product = new Product($db);
                $product->id = $item['product_id'];
                $product_result = $product->findOne();
                ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $product_result[2]['name']; ?></td>
                    <td><?php echo $item['qty'] ?></td>
                    <td><?php echo $item['price'] ?></td>
                </tr>
                <?php
                $total += $item['qty'] * $item['price'];
            endforeach;
        endif;
        ?>
        <tr>
            <th class="text-center" colspan="3">ราคารวม</th>
            <th class="text-center"><?php echo $total; ?></th>
        </tr>
        </tbody>
    </table>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
