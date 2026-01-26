<?php
require "db.php";

$name  = $_POST['name'];
$price = $_POST['price'];
$qty   = $_POST['qty'];

$stmt = $conn->prepare("INSERT INTO products (name, price, qty) VALUES (?, ?, ?)");
$stmt->bind_param("sdi", $name, $price, $qty);
$stmt->execute();

echo "Product added";
