<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include("config/db.php");

// Function to get smallest available ID
function getAvailableID($conn) {
    $ids = [];
    $result = mysqli_query($conn, "SELECT id FROM products ORDER BY id ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        $ids[] = $row['id'];
    }
    $id = 1;
    foreach ($ids as $used) {
        if ($used == $id) {
            $id++;
        } else break;
    }
    return $id;
}

// Add product
if (isset($_POST['add_product'])) {
    $id = getAvailableID($conn);
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    mysqli_query($conn, "INSERT INTO products (id, name, category, price, quantity) VALUES ($id,'$name','$category','$price','$quantity')");
}

// Delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: inventory.php");
    exit();
}

// Edit product
$editProduct = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    mysqli_query($conn,"UPDATE products SET name='$name', category='$category', price='$price', quantity='$quantity' WHERE id=$id");
    header("Location: inventory.php");
    exit();
}

// Fetch products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory | Smart Inventory</title>
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
            <li><a href="billing.php">💵 Billing</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">Add New Product</h1>
        <button id="darkToggle" class="dark-toggle">🌙</button>


        <!-- Add/Edit Product Card (Centered) -->
        <div class="card-form center-card">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $editProduct['id'] ?? ''; ?>">
                <input type="text" name="name" placeholder="Product Name" required value="<?php echo $editProduct['name'] ?? ''; ?>">
                <input type="text" name="category" placeholder="Category" required value="<?php echo $editProduct['category'] ?? ''; ?>">
                <input type="number" name="price" placeholder="Price (₹)" required value="<?php echo $editProduct['price'] ?? ''; ?>">
                <input type="number" name="quantity" placeholder="Quantity" required value="<?php echo $editProduct['quantity'] ?? ''; ?>">
                <?php if($editProduct){ ?>
                    <button type="submit" name="update_product">Update Product</button>
                <?php } else { ?>
                    <button type="submit" name="add_product">Add Product</button>
                <?php } ?>
            </form>
        </div>

        <!-- Products Table (Horizontal Card/Table) -->
        <div class="table-card">
            <table class="data-table">
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while($row=mysqli_fetch_assoc($products)){ 
                    $status = $row['quantity']>0 ? '✅ Available' : '❌ Not Available';
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td>₹ <?php echo $row['price']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $status; ?></td>
                    <td>
                        <a href="inventory.php?edit=<?php echo $row['id']; ?>" class="edit-btn">✏️</a>
                        <a href="inventory.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">🗑️</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

    </div>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>
