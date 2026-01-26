<?php
require "db.php";

$items = json_decode($_POST['items'], true);
$cash  = $_POST['cash'];
$pos   = $_POST['pos'];
$bank  = $_POST['bank'];

foreach ($items as $item) {
    $stmt = $conn->prepare("
      INSERT INTO sales (product_name, qty, unit_price, total_price, cash, pos, bank)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $total = $item['price'] * $item['qty'];
    $stmt->bind_param(
        "siddddd",
        $item['name'],
        $item['qty'],
        $item['price'],
        $total,
        $cash,
        $pos,
        $bank
    );
    $stmt->execute();

    // Reduce stock
    $stmt2 = $conn->prepare("UPDATE products SET qty = qty - ? WHERE name=?");
    $stmt2->bind_param("is", $item['qty'], $item['name']);
    $stmt2->execute();
}

echo "Checkout successful";
