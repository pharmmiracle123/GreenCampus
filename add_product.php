<?php
// add_product.php
header('Content-Type: text/plain');

// --- Include database connection ---
require_once "db.php"; // This will use your $conn from db.php

// --- Read POST JSON ---
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) { 
    echo "Invalid request"; 
    exit; 
}

$name = trim($data['name'] ?? '');
$price = floatval($data['price'] ?? 0);
$stock = intval($data['stock'] ?? 0);
$password = $data['password'] ?? '';

// --- Admin Password Check ---
$ADMIN_PASSWORD = "ADMIN123"; // For better security, move to env variable
if ($password !== $ADMIN_PASSWORD) {
    echo "Wrong password";
    exit;
}

// --- Validate Inputs ---
if (!$name || $price <= 0 || $stock < 0) {
    echo "Invalid product data";
    exit;
}

// --- Insert Product ---
$stmt = $conn->prepare("INSERT INTO products (name, price, qty) VALUES (?, ?, ?)");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("sdi", $name, $price, $stock);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
