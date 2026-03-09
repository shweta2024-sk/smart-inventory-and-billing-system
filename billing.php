<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include("config/db.php");

/* Generate Invoice Number and Date */
$invoiceNo = "INV-" . date("Ymd") . "-" . rand(1000,9999);


/* Fetch all products into array to reuse */
$productList = [];
$result = mysqli_query($conn, "SELECT * FROM products WHERE quantity > 0 ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $productList[] = $row;
}

$invoiceItems = [];
$grandTotal = 0;
$showInvoice = false;

/* Generate Bill */
if (isset($_POST['generate_bill'])) {
    $invoiceDate = date("d M Y, h:i A");
    if (!empty($_POST['product_id'])) {
        foreach ($_POST['product_id'] as $i => $pid) {
            $qty = $_POST['qty'][$i];
            if ($pid && $qty > 0) {
                $pRes = mysqli_query($conn, "SELECT * FROM products WHERE id=$pid");
                $p = mysqli_fetch_assoc($pRes);

                if ($p && $p['quantity'] >= $qty) {
                    $total = $p['price'] * $qty;
                    $grandTotal += $total;

                    $invoiceItems[] = [
                        'name'  => $p['name'],
                        'price' => $p['price'],
                        'qty'   => $qty,
                        'total' => $total
                    ];

                    /* Save Sale */
                    mysqli_query($conn,
                        "INSERT INTO sales (product_id, quantity, total)
                         VALUES ($pid, $qty, $total)"
                    );

                    /* Update Stock */
                    mysqli_query($conn,
                        "UPDATE products
                         SET quantity = quantity - $qty
                         WHERE id = $pid"
                    );
                }
            }
        }

        if ($grandTotal > 0) {
            $showInvoice = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Billing | Smart Inventory</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="/billing_system/assets/css/animations.css">
</head>
<body>

<div class="wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Smart Inventory</h2>
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="inventory.php">📦 Inventory</a></li>
            <li><a href="billing.php" class="active">💵 Billing</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <h1 class="page-title">Add Product to Bill</h1>
         <button id="darkToggle" class="dark-toggle">🌙</button>


        <!-- Billing Form -->
        <div class="card-form billing-box">
            <form method="post">

                <div id="bill-rows">
                    <div class="bill-row">
                        <select name="product_id[]" required>
                            <option value="">Select Product</option>
                            <?php foreach ($productList as $p) { ?>
                                <option value="<?php echo $p['id']; ?>">
                                    <?php echo $p['name']; ?> (₹<?php echo $p['price']; ?>)
                                </option>
                            <?php } ?>
                        </select>
                        <input type="number" name="qty[]" min="1" placeholder="Quantity" required>
                    </div>
                </div>

                <button type="button" class="add-row-btn" onclick="addRow()">➕ Add Product</button>
                <button type="submit" name="generate_bill" class="generate-btn">🧾 Generate Bill</button>

            </form>
        </div>

        <!-- Invoice -->
        <?php if ($showInvoice) { ?>
        <div class="card-form invoice-box" id="invoice">

            <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                <div><strong>Invoice No:</strong> <?php echo $invoiceNo; ?></div>
                <div><strong>Date:</strong> <?php echo $invoiceDate; ?></div>
            </div>

            <table class="data-table">
                <tr>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Qty</th>
                    <th>Total (₹)</th>
                </tr>

                <?php foreach ($invoiceItems as $item) { ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['price']; ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td><?php echo $item['total']; ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <th colspan="3">Grand Total</th>
                    <th>₹ <?php echo $grandTotal; ?></th>
                </tr>
            </table>

            <button onclick="printInvoice()" class="generate-btn">🖨️ Print Invoice</button>
        </div>
        <?php } ?>

    </div>
</div>

<script>
function addRow() {
    let html = `
    <div class="bill-row">
        <select name="product_id[]" required>
            <option value="">Select Product</option>
            <?php foreach ($productList as $p) { ?>
                <option value="<?php echo $p['id']; ?>">
                    <?php echo $p['name']; ?> (₹<?php echo $p['price']; ?>)
                </option>
            <?php } ?>
        </select>
        <input type="number" name="qty[]" min="1" placeholder="Quantity" required>
    </div>`;
    document.getElementById("bill-rows").insertAdjacentHTML("beforeend", html);
}

function printInvoice() {
    let content = document.getElementById("invoice").innerHTML;
    let win = window.open('', '', 'width=900,height=650');
    win.document.write(`
        <html>
        <head>
            <title>Invoice</title>
            <style>
                body { font-family: Arial; padding: 20px; }
                table { width:100%; border-collapse: collapse; margin-top:20px; }
                th, td { border:1px solid #000; padding:8px; text-align:center; }
                th { background:#f1f5f9; }
            </style>
        </head>
        <body>${content}</body>
        </html>
    `);
    win.document.close();
    win.print();

    // Reset page for next bill
    setTimeout(() => { window.location.href = "billing.php"; }, 500);
}
</script>
<script src="assets/js/script.js"></script>
</body>
</html>
