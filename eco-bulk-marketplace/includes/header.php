<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoBulk — Eco-Friendly Bulk Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="/eco-bulk-marketplace/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/eco-bulk-marketplace/index.php">EcoBulk</a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/eco-bulk-marketplace/index.php">Home</a>
                </li>

                <?php if (!isset($_SESSION['user_id'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item ms-1">
                        <a class="nav-link" href="/eco-bulk-marketplace/auth/register.php"
                           style="background:rgba(255,255,255,0.12); border-radius:8px; padding:0.5rem 1.1rem !important;">
                            Register
                        </a>
                    </li>
                <?php } ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/admin/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/admin/products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/admin/orders.php">Orders</a>
                    </li>
                <?php } ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'customer') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/customer/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/customer/products.php">Browse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/eco-bulk-marketplace/customer/bulk_groups.php">Bulk Groups</a>
                    </li>
                <?php } ?>

                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li class="nav-item ms-2">
                        <a class="nav-link" href="/eco-bulk-marketplace/logout.php"
                           style="opacity:0.7; font-size:0.82rem;">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                <?php } ?>

            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">