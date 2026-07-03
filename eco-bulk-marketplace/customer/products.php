<?php
include "../includes/config.php";
include "../includes/functions.php";
requireCustomer();

$message = "";

if (isset($_POST["place_order"])) {
    $customer_id = $_SESSION["user_id"];
    $product_id = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    $payment_method = $_POST["payment_method"];

    $stmt = $conn->prepare("CALL place_order(?, ?, ?, NULL, ?)");
    $stmt->bind_param("iiis", $customer_id, $product_id, $quantity, $payment_method);

    if ($stmt->execute()) {
        $message = "Order placed successfully.";
    } else {
        $message = "Order could not be placed. Please check stock availability.";
    }

    while ($conn->more_results() && $conn->next_result()) {}
}

$search = "";
$where = "";

if (isset($_GET["search"]) && $_GET["search"] != "") {
    $search = sanitize($_GET["search"]);
    $where = "WHERE p.product_name LIKE '%$search%' OR c.category_name LIKE '%$search%'";
}

$products = $conn->query("
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    $where
    ORDER BY p.product_id DESC
");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Customer Portal</p>
        <h2 class="mb-0">Browse Products</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
</div>

<?php if ($message != "") { ?>
    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
<?php } ?>

<!-- Search Bar -->
<form method="GET" class="mt-3 mb-4">
    <div class="search-bar">
        <i class="bi bi-search"></i>
        <input type="text" name="search" class="form-control" placeholder="Search products or categories..."
               value="<?php echo $search; ?>">
        <button class="btn btn-success" type="submit">Search</button>
    </div>
</form>

<!-- Product Grid -->
<div class="row g-4">
    <?php while ($row = $products->fetch_assoc()) { ?>
        <div class="col-md-4">
            <div class="card product-card p-4 h-100">

                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h4 class="mb-0" style="color:var(--ink);"><?php echo $row["product_name"]; ?></h4>
                    <?php
                    $ecoClass = strtolower($row["eco_rating"]);
                    echo "<span class='eco-badge eco-badge-{$ecoClass}'>{$row['eco_rating']}</span>";
                    ?>
                </div>

                <p class="mb-3" style="font-size:0.875rem;"><?php echo $row["description"]; ?></p>

                <div class="product-meta">
                    <div class="product-meta-row">
                        <span><i class="bi bi-tag me-1"></i>Category</span>
                        <strong><?php echo $row["category_name"]; ?></strong>
                    </div>
                    <div class="product-meta-row">
                        <span><i class="bi bi-currency-dollar me-1"></i>Price</span>
                        <strong style="color:var(--emerald);">Rs. <?php echo number_format($row["price"], 2); ?></strong>
                    </div>
                    <div class="product-meta-row">
                        <span><i class="bi bi-box me-1"></i>Stock</span>
                        <strong><?php echo $row["stock_quantity"]; ?> units</strong>
                    </div>
                </div>

                <?php if ($row["stock_quantity"] > 0) { ?>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="product_id" value="<?php echo $row["product_id"]; ?>">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label style="font-size:0.72rem;">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="1"
                                    max="<?php echo $row["stock_quantity"]; ?>" placeholder="Qty" required>
                            </div>
                            <div class="col-6">
                                <label style="font-size:0.72rem;">Payment</label>
                                <select name="payment_method" class="form-control">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-success w-100">
                            <i class="bi bi-bag-plus me-1"></i> Place Order
                        </button>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-danger mt-3 mb-0 py-2 text-center" style="font-size:0.82rem;">
                        <i class="bi bi-x-circle me-1"></i> Out of Stock
                    </div>
                <?php } ?>

            </div>
        </div>
    <?php } ?>
</div>

<?php include "../includes/footer.php"; ?>