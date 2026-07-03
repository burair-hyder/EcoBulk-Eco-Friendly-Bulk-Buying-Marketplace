<?php
include "../includes/config.php";
include "../includes/functions.php";
requireCustomer();

$customer_id = $_SESSION["user_id"];

$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE customer_id = $customer_id")->fetch_assoc()["total"];
$totalGroups = $conn->query("SELECT COUNT(*) AS total FROM group_members WHERE customer_id = $customer_id")->fetch_assoc()["total"];
?>

<?php include "../includes/header.php"; ?>

<!-- Page Header -->
<div class="dash-header">
    <div>
        <p class="dash-kicker">Customer Portal</p>
        <h2 class="mb-0">Welcome back, <?php echo $_SESSION["full_name"]; ?> 👋</h2>
    </div>
    <span class="dash-date"><i class="bi bi-calendar3 me-1"></i><?php echo date("d M Y"); ?></span>
</div>

<!-- Stat Cards -->
<div class="row g-3 mt-1">
    <div class="col-sm-6">
        <div class="dashboard-card">
            <div class="dash-card-icon" style="background:rgba(251,191,89,0.1);color:#FBBF59;">
                <i class="bi bi-receipt"></i>
            </div>
            <h4><?php echo $totalOrders; ?></h4>
            <p>My Orders</p>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="dashboard-card">
            <div class="dash-card-icon" style="background:rgba(61,220,154,0.1);color:#3DDC9A;">
                <i class="bi bi-people-fill"></i>
            </div>
            <h4><?php echo $totalGroups; ?></h4>
            <p>Joined Bulk Groups</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<h3 class="dash-section-title">Quick Actions</h3>

<div class="row g-3">
    <div class="col-sm-6 col-lg-3">
        <a href="products.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(61,220,154,0.1);color:#3DDC9A;">
                <i class="bi bi-shop"></i>
            </div>
            <div>
                <div class="dash-action-title">Browse Products</div>
                <div class="dash-action-sub">Explore eco-friendly listings</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="bulk_groups.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(96,165,250,0.1);color:#60A5FA;">
                <i class="bi bi-collection"></i>
            </div>
            <div>
                <div class="dash-action-title">Bulk Groups</div>
                <div class="dash-action-sub">Join or create group orders</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="my_orders.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(251,191,89,0.1);color:#FBBF59;">
                <i class="bi bi-bag-check"></i>
            </div>
            <div>
                <div class="dash-action-title">My Orders</div>
                <div class="dash-action-sub">Track your order history</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>

    <div class="col-sm-6 col-lg-3">
        <a href="profile.php" class="dash-action-card">
            <div class="dash-action-icon" style="background:rgba(217,193,140,0.1);color:#D9C18C;">
                <i class="bi bi-person-circle"></i>
            </div>
            <div>
                <div class="dash-action-title">My Profile</div>
                <div class="dash-action-sub">Update your account info</div>
            </div>
            <i class="bi bi-arrow-right ms-auto"></i>
        </a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>