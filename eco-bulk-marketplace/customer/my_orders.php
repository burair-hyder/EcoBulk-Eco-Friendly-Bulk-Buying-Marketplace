<?php
include "../includes/config.php";
include "../includes/functions.php";
requireCustomer();

$customer_id = $_SESSION["user_id"];

$orders = $conn->query("
    SELECT 
        o.order_id,
        o.total_amount,
        o.order_status,
        o.order_date,
        p.payment_method,
        p.payment_status,
        GROUP_CONCAT(pr.product_name ORDER BY pr.product_name SEPARATOR ', ') AS products_ordered
    FROM orders o
    LEFT JOIN payments p ON o.order_id = p.order_id
    LEFT JOIN order_details od ON o.order_id = od.order_id
    LEFT JOIN products pr ON od.product_id = pr.product_id
    WHERE o.customer_id = $customer_id
    GROUP BY o.order_id, o.total_amount, o.order_status, o.order_date, p.payment_method, p.payment_status
    ORDER BY o.order_id DESC
");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Customer Portal</p>
        <h2 class="mb-0">My Orders</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
</div>

<h3 class="dash-section-title">Order History</h3>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Items Ordered</th>
                <th>Amount</th>
                <th>Order Status</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch_assoc()) {
                $s = strtolower($row["order_status"]);
                $sCls = match($s) {
                    "confirmed"  => "status-confirmed",
                    "shipped"    => "status-shipped",
                    "delivered"  => "status-delivered",
                    "cancelled"  => "status-cancelled",
                    default      => "status-pending"
                };
                $pStatus = strtolower($row["payment_status"] ?? "");
                $pCls = $pStatus === "paid" ? "status-confirmed" : "status-pending";
            ?>
                <tr>
                    <td><span style="color:var(--slate-dim);font-size:0.8rem;">#<?php echo $row["order_id"]; ?></span></td>
                    <td>
                        <span style="font-weight:600;color:var(--ink);">
                            <?php echo $row["products_ordered"] ?: "—"; ?>
                        </span>
                    </td>
                    <td style="color:var(--emerald);font-weight:600;">Rs. <?php echo number_format($row["total_amount"], 2); ?></td>
                    <td><span class="status-badge <?php echo $sCls; ?>"><?php echo $row["order_status"]; ?></span></td>
                    <td>
                        <div style="font-size:0.82rem;margin-bottom:3px;"><?php echo $row["payment_method"]; ?></div>
                        <span class="status-badge <?php echo $pCls; ?>"><?php echo $row["payment_status"] ?? "—"; ?></span>
                    </td>
                    <td style="font-size:0.82rem;color:var(--slate-dim);"><?php echo date("d M Y", strtotime($row["order_date"])); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>