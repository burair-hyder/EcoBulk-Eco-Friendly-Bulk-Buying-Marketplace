<?php
include "../includes/config.php";
include "../includes/functions.php";
requireAdmin();

$message = "";

if (isset($_POST["add_product"])) {
    $category_id = $_POST["category_id"];
    $product_name = sanitize($_POST["product_name"]);
    $description = sanitize($_POST["description"]);
    $price = $_POST["price"];
    $stock_quantity = $_POST["stock_quantity"];
    $eco_rating = $_POST["eco_rating"];

    $stmt = $conn->prepare("
        INSERT INTO products (category_id, product_name, description, price, stock_quantity, eco_rating)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issdis", $category_id, $product_name, $description, $price, $stock_quantity, $eco_rating);

    if ($stmt->execute()) {
        $message = "Product added successfully.";
    } else {
        $message = "Product could not be added.";
    }
}

if (isset($_POST["update_product"])) {
    $product_id = $_POST["product_id"];
    $category_id = $_POST["category_id"];
    $product_name = sanitize($_POST["product_name"]);
    $description = sanitize($_POST["description"]);
    $price = $_POST["price"];
    $stock_quantity = $_POST["stock_quantity"];
    $eco_rating = $_POST["eco_rating"];

    $stmt = $conn->prepare("
        UPDATE products 
        SET category_id = ?, product_name = ?, description = ?, price = ?, stock_quantity = ?, eco_rating = ?
        WHERE product_id = ?
    ");
    $stmt->bind_param("issdisi", $category_id, $product_name, $description, $price, $stock_quantity, $eco_rating, $product_id);

    if ($stmt->execute()) {
        $message = "Product updated successfully.";
    } else {
        $message = "Product could not be updated.";
    }
}

if (isset($_GET["delete"])) {
    $product_id = $_GET["delete"];

    $checkOrders = $conn->prepare("SELECT COUNT(*) AS total FROM order_details WHERE product_id = ?");
    $checkOrders->bind_param("i", $product_id);
    $checkOrders->execute();
    $orderCount = $checkOrders->get_result()->fetch_assoc()["total"];

    $checkGroups = $conn->prepare("SELECT COUNT(*) AS total FROM bulk_groups WHERE product_id = ?");
    $checkGroups->bind_param("i", $product_id);
    $checkGroups->execute();
    $groupCount = $checkGroups->get_result()->fetch_assoc()["total"];

    if ($orderCount > 0) {
        $message = "This product cannot be deleted because it already exists in customer orders. You can update its stock instead.";
    } elseif ($groupCount > 0) {
        $message = "This product cannot be deleted because it is linked with bulk buying groups.";
    } else {
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            $message = "Product deleted successfully.";
        } else {
            $message = "Product could not be deleted.";
        }
    }
}

$editProduct = null;

if (isset($_GET["edit"])) {
    $product_id = $_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $editProduct = $stmt->get_result()->fetch_assoc();
}

$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

$products = $conn->query("
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.product_id DESC
");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Admin Panel</p>
        <h2 class="mb-0">Manage Products</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<?php if ($message != "") { ?>
    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
<?php } ?>

<div class="card p-4 mb-4 mt-3">
    <h4><?php echo $editProduct ? "Update Product" : "Add New Product"; ?></h4>

    <form method="POST">
        <?php if ($editProduct) { ?>
            <input type="hidden" name="product_id" value="<?php echo $editProduct["product_id"]; ?>">
        <?php } ?>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php while ($cat = $categories->fetch_assoc()) { ?>
                            <option value="<?php echo $cat["category_id"]; ?>"
                                <?php echo ($editProduct && $editProduct["category_id"] == $cat["category_id"]) ? "selected" : ""; ?>>
                                <?php echo $cat["category_name"]; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="product_name" class="form-control"
                        value="<?php echo $editProduct ? $editProduct["product_name"] : ""; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"><?php echo $editProduct ? $editProduct["description"] : ""; ?></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label>Price (Rs.)</label>
                    <input type="number" step="0.01" name="price" class="form-control"
                        value="<?php echo $editProduct ? $editProduct["price"] : ""; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control"
                        value="<?php echo $editProduct ? $editProduct["stock_quantity"] : ""; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Eco Rating</label>
                    <select name="eco_rating" class="form-control">
                        <option value="Low"    <?php echo ($editProduct && $editProduct["eco_rating"] == "Low")    ? "selected" : ""; ?>>Low</option>
                        <option value="Medium" <?php echo ($editProduct && $editProduct["eco_rating"] == "Medium") ? "selected" : ""; ?>>Medium</option>
                        <option value="High"   <?php echo ($editProduct && $editProduct["eco_rating"] == "High")   ? "selected" : ""; ?>>High</option>
                    </select>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <?php if ($editProduct) { ?>
                        <button type="submit" name="update_product" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Update Product
                        </button>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    <?php } else { ?>
                        <button type="submit" name="add_product" class="btn btn-success">
                            <i class="bi bi-plus-lg me-1"></i> Add Product
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </form>
</div>

<h3 class="dash-section-title">All Products</h3>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Eco Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $products->fetch_assoc()) { ?>
                <tr>
                    <td><span style="color:var(--slate-dim);font-size:0.8rem;">#<?php echo $row["product_id"]; ?></span></td>
                    <td style="font-weight:600;color:var(--ink);"><?php echo $row["product_name"]; ?></td>
                    <td><?php echo $row["category_name"]; ?></td>
                    <td style="color:var(--emerald);font-weight:600;">Rs. <?php echo number_format($row["price"], 2); ?></td>
                    <td><?php echo $row["stock_quantity"]; ?></td>
                    <td>
                        <?php
                        $ecoClass = strtolower($row["eco_rating"]);
                        echo "<span class='eco-badge eco-badge-{$ecoClass}'>{$row['eco_rating']}</span>";
                        ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="products.php?edit=<?php echo $row["product_id"]; ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="products.php?delete=<?php echo $row["product_id"]; ?>"
                               onclick="return confirmDelete();" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>