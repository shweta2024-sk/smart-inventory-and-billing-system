<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include("config/db.php");

// Info Boxes
$productCount = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products")
)['total'];

$lowStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE quantity < 5")
)['total'];

$salesTotal = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total) AS total FROM sales")
)['total'];

$todaySales = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total) AS total FROM sales WHERE sale_date = CURDATE()")
)['total'];

$weekSales = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total) AS total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")
)['total'];

// Top selling products (quantity sold)
$topProducts = mysqli_query(
    $conn,
    "SELECT p.name, SUM(s.quantity) AS qty_sold
     FROM sales s
     JOIN products p ON s.product_id = p.id
     GROUP BY s.product_id
     ORDER BY qty_sold DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Smart Inventory</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="/billing_system/assets/css/animations.css">
   
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Page Loader -->
<div id="page-loader">
    <div class="loader"></div>
</div>

<div class="wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        
        <ul>
            <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="inventory.php">📦 Inventory</a></li>
            <li><a href="billing.php">💵 Billing</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Page Title -->
        <h1 class="page-title">Smart Inventory System</h1>
        <button id="darkToggle" class="dark-toggle">🌙</button>

        <!-- Info Boxes -->
        <div class="info-boxes">
            <div class="info-box">📦<br>Total Products<br><span><?php echo $productCount ?? 0; ?></span></div>
            <div class="info-box">⚠️<br>Low Stock Items<br><span><?php echo $lowStock ?? 0; ?></span></div>
            <div class="info-box">💰<br>Total Sales<br><span>₹ <?php echo $salesTotal ?? 0; ?></span></div>
            <div class="info-box">📅<br>Today's Sales<br><span>₹ <?php echo $todaySales ?? 0; ?></span></div>
            <div class="info-box">🗓️<br>Weekly Sales<br><span>₹ <?php echo $weekSales ?? 0; ?></span></div>
        </div>
<!-- Top Selling Products Table -->
<div class="top-products">
    <h2>Top Selling Products</h2>
    <table class="top-products-table">
        <thead>
            <tr>
                <th>📦 Product</th>
                <th>Qty Sold</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($topProducts)) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['qty_sold']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

       

</div>

</div>
 <script src="assets/js/script.js"></script>
</body>
</html>
