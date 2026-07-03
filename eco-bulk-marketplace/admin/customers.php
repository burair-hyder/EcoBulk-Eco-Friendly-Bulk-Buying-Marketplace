<?php
include "../includes/config.php";
include "../includes/functions.php";
requireAdmin();

$customers = $conn->query("
    SELECT 
        customer_id,
        full_name,
        email,
        phone,
        address,
        created_at
    FROM customers
    ORDER BY customer_id DESC
");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Admin Panel</p>
        <h2 class="mb-0">Registered Customers</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<h3 class="dash-section-title">All Customers</h3>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $customers->fetch_assoc()) { ?>
                <tr>
                    <td><span style="color:var(--slate-dim);font-size:0.8rem;">#<?php echo $row["customer_id"]; ?></span></td>
                    <td>
                        <div style="font-weight:600;color:var(--ink);"><?php echo $row["full_name"]; ?></div>
                        <div style="font-size:0.78rem;color:var(--slate-dim);"><?php echo $row["email"]; ?></div>
                    </td>
                    <td style="font-size:0.875rem;"><?php echo $row["phone"] ?: "—"; ?></td>
                    <td style="font-size:0.82rem;max-width:200px;"><?php echo $row["address"] ?: "—"; ?></td>
                    <td style="font-size:0.82rem;color:var(--slate-dim);"><?php echo date("d M Y", strtotime($row["created_at"])); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>