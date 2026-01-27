<?php
// update_stock.php
header('Content-Type: text/plain');

// --- Include database connection ---
require_once "db.php"; // uses $conn

// --- Read POST JSON ---
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) { 
    echo "Invalid request"; 
    exit; 
}

$id = intval($data['id'] ?? 0);
$change = intval($data['change'] ?? 0);

// --- Validate Inputs ---
if ($id <= 0) { 
    echo "Invalid product ID"; 
    exit; 
}

// --- Update Stock ---
$stmt = $conn->prepare("UPDATE products SET qty = qty + ? WHERE id = ?");
if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param("ii", $change, $id);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
