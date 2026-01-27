<?php
include "db.php";
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['cart'], $input['payment'])) {
    echo "Invalid request";
    exit;
}

$conn->begin_transaction();

try {
    foreach ($input['cart'] as $item) {
        $total = $item['price'] * $item['qty'];

        // Insert into sales
        $stmt = $conn->prepare("
            INSERT INTO sales (product_id, qty, total, cash, pos, bank)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iddddd",
            $item['id'],
            $item['qty'],
            $total,
            $input['payment']['cash'],
            $input['payment']['pos'],
            $input['payment']['bank']
        );
        $stmt->execute();

        // Update product stock safely
        $update = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update->bind_param("ii", $item['qty'], $item['id']);
        $update->execute();
    }

    $conn->commit();
    echo "ok";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
