<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include("config/db.php");

// Get last sale
$sale = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT s.*, p.name AS product_name
                         FROM sales s
                         JOIN products p ON s.product_id = p.id
                         ORDER BY s.id DESC
                         LIMIT 1")
);

if (!$sale) {
    echo "No sales found!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Bill</title>
    <style>
        body { font-family: Arial; }
        .bill { width: 400px; margin: auto; border: 1px solid #000; padding: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        button { margin-top: 20px; width: 100%; padding: 10px; cursor: pointer; }
    </style>
</head>
<body>

<div class="bill">
    <h2>Billing System</h2>
    <table>
        <tr>
            <th>Product</th>
            <td><?php echo $sale['product_name']; ?></td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td><?php echo $sale['quantity']; ?></td>
        </tr>
        <tr>
            <th>Total</th>
            <td>₹ <?php echo $sale['total']; ?></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?php echo $sale['sale_date']; ?></td>
        </tr>
    </table>

    <button onclick="window.print();">Print Bill</button>
</div>

</body>
</html>
