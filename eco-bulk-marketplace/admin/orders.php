<?php
include "../includes/config.php";
include "../includes/functions.php";
requireAdmin();

$message = "";

if (isset($_POST["update_status"])) {
    $order_id = $_POST["order_id"];
    $order_status = $_POST["order_status"];

    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $order_status, $order_id);

    if ($stmt->execute()) {
        $message = "Order status updated successfully.";
    } else {
        $message = "Order status could not be updated.";
    }
}

$orders = $conn->query("
    SELECT 
        o.order_id,
        c.full_name,
        c.email,
        o.total_amount,
        o.order_status,
        o.order_date,
        p.payment_method,
        p.payment_status
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    LEFT JOIN payments p ON o.order_id = p.order_id
    ORDER BY o.order_id DESC
");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Admin Panel</p>
        <h2 class="mb-0">Manage Orders</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<?php if ($message != "") { ?>
    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
<?php } ?>

<h3 class="dash-section-title">All Orders</h3>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Order Status</th>
                <th>Date</th>
                <th>Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch_assoc()) { ?>
                <tr>
                    <td><span style="color:var(--slate-dim);font-size:0.8rem;">#<?php echo $row["order_id"]; ?></span></td>
                    <td>
                        <div style="font-weight:600;color:var(--ink);"><?php echo $row["full_name"]; ?></div>
                        <div style="font-size:0.78rem;color:var(--slate-dim);"><?php echo $row["email"]; ?></div>
                    </td>
                    <td style="color:var(--emerald);font-weight:600;">Rs. <?php echo number_format($row["total_amount"], 2); ?></td>
                    <td>
                        <div style="font-size:0.82rem;"><?php echo $row["payment_method"]; ?></div>
                        <?php
                        $pStatus = strtolower($row["payment_status"] ?? "");
                        $pClass = $pStatus === "paid" ? "status-confirmed" : "status-pending";
                        echo "<span class='status-badge {$pClass}'>" . ($row["payment_status"] ?? "—") . "</span>";
                        ?>
                    </td>
                    <td>
                        <?php
                        $s = strtolower($row["order_status"]);
                        $cls = match($s) {
                            "confirmed"  => "status-confirmed",
                            "shipped"    => "status-shipped",
                            "delivered"  => "status-delivered",
                            "cancelled"  => "status-cancelled",
                            default      => "status-pending"
                        };
                        echo "<span class='status-badge {$cls}'>{$row['order_status']}</span>";
                        ?>
                    </td>
                    <td style="font-size:0.82rem;color:var(--slate-dim);"><?php echo date("d M Y", strtotime($row["order_date"])); ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="order_id" value="<?php echo $row["order_id"]; ?>">
                            <select name="order_status" class="form-control form-control-sm" style="min-width:130px;">
                                <?php foreach(["Pending","Confirmed","Shipped","Delivered","Cancelled"] as $status) { ?>
                                    <option value="<?php echo $status; ?>"
                                        <?php echo $row["order_status"] == $status ? "selected" : ""; ?>>
                                        <?php echo $status; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-success">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>