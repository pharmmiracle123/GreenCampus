<?php
require "db.php";

$name = $_POST['name'];
$qty  = $_POST['qty'];

$stmt = $conn->prepare("UPDATE products SET qty=? WHERE name=?");
$stmt->bind_param("is", $qty, $name);
$stmt->execute();

echo "Stock updated";
