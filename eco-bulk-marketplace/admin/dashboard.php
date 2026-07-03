<?php
include "../includes/config.php";
include "../includes/functions.php";
requireAdmin();

$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()["total"];
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()["total"];
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()["total"];
$totalGroups = $conn->query("SELECT COUNT(*) AS total FROM bulk_groups")->fetch_assoc()["total"];
?>

<?php include "../includes/header.php"; ?>

<!-- Page Header -->
<div class="dash-header">
    <div>
        <p class="dash-kicker">Admin Panel</p>
        <h2 class="mb-0">Dashboard</h2>
    </div>
    <div class="dash-meta">
        <span><i class="bi bi-person-circle me-1"></i><?php echo $_SESSION["full_name"]; ?></span>
        <span class="dash-date"><i class="bi bi-calendar3 me-1"></i><?php echo date("d M Y"); ?></span>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mt-1">
    <div class="col-sm-6 col-lg-3">
        <div class="dashboard-card">
            <div class="dash-card-icon" style="background:rgba(61,220,154,0.1);color:#3DDC9A;">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <h4><?php echo $totalProducts; ?></h4>
            <p>Total Products</p>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="dashboard-card">
            <div class="dash-card-icon" style="background:rgba(96,165,250,0.1);color:#60A5FA;">
                <i class="bi bi-people-fill"></i>
            </div>
            <h4><?php echo $totalCustomers; ?></h4>
            <p>Total Customers</p>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="dashboard-card">
            <div class="dash-card-icon" style="background:rgba(251,191,89,0.1);color:#FBBF59;">
                <i class="bi bi-receipt"></i>
            </div>
            <h4><?php echo $totalOrders; ?></h4>
            <p>Total Orders</p>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="dashboard-card">
            <div class="dash-card-icon" style="background:rgba(217,193,140,0.1);color:#D9C18C;">
                <i class="bi bi-collection-fill"></i>
            </div>
            <h4><?php echo $totalGroups; ?></h4>
            <p>Bulk Groups</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<h3 class="dash-section-title">Quick Actions</h3>

<div class="row g-3">
    <div class="col-sm-6 col-lg-3">
        <a href="products.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(61,220,154,0.1);color:#3DDC9A;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="dash-action-title">Manage Products</div>
                <div class="dash-action-sub">Add, edit or remove listings</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="categories.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(96,165,250,0.1);color:#60A5FA;">
                <i class="bi bi-tag"></i>
            </div>
            <div>
                <div class="dash-action-title">Manage Categories</div>
                <div class="dash-action-sub">Organise product categories</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="orders.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(251,191,89,0.1);color:#FBBF59;">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <div class="dash-action-title">View Orders</div>
                <div class="dash-action-sub">Track and update order status</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="customers.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(217,193,140,0.1);color:#D9C18C;">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="dash-action-title">View Customers</div>
                <div class="dash-action-sub">Browse registered accounts</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>