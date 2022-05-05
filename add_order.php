<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Guard.php");
    require_once("controllers/Order.php");
    require_once("controllers/DetailOrder.php");
    require_once("controllers/Product.php");
    Guard::member();
    $db = DB::conn();
    $data = json_decode(file_get_contents("php://input"), true);
        $products = json_decode($data['products'], true);
        $order = new Order($db);
        $order->memberID = $data['customer'];
        $order->received = $data['received'];
        $order->total = $data['total'];
        $order->create();
        $order_result = $order->findLast();
        foreach ($products as $item) {
            $detailOrder = new DetailOrder($db);
            $detailOrder->orderID = $order_result[2]['id'];
            $detailOrder->qty = $item['qty'];
            $detailOrder->price = $item['price'];
            $detailOrder->productID = $item['id'];
            $detailOrder->create();

            $product = new Product($db);
            $product->id = $item['id'];
            $product->UpdateStock($item['qty']);
        }
