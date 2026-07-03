<?php
include "../includes/config.php";
include "../includes/functions.php";
requireCustomer();

$message = "";
$customer_id = $_SESSION["user_id"];

if (isset($_POST["create_group"])) {
    $product_id = $_POST["product_id"];
    $target_quantity = $_POST["target_quantity"];
    $discount_percentage = $_POST["discount_percentage"];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO bulk_groups 
            (product_id, created_by, target_quantity, ordered_quantity, current_members, discount_percentage, status)
            VALUES (?, ?, ?, 0, 1, ?, 'Open')
        ");
        $stmt->bind_param("iiid", $product_id, $customer_id, $target_quantity, $discount_percentage);
        $stmt->execute();

        $group_id = $conn->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO group_members (group_id, customer_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $group_id, $customer_id);
        $stmt2->execute();

        $conn->commit();
        $message = "Bulk group created successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Bulk group could not be created.";
    }
}

if (isset($_GET["join"])) {
    $group_id = $_GET["join"];

    $check = $conn->prepare("SELECT membership_id FROM group_members WHERE group_id = ? AND customer_id = ?");
    $check->bind_param("ii", $group_id, $customer_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "You have already joined this bulk group.";
    } else {
        $stmt = $conn->prepare("CALL join_bulk_group(?, ?)");
        $stmt->bind_param("ii", $group_id, $customer_id);

        if ($stmt->execute()) {
            $message = "Joined bulk group successfully.";
        } else {
            $message = "Could not join group.";
        }

        while ($conn->more_results() && $conn->next_result()) {}
    }
}

if (isset($_POST["order_from_group"])) {
    $group_id = $_POST["group_id"];
    $product_id = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    $payment_method = $_POST["payment_method"];

    $checkMember = $conn->prepare("SELECT membership_id FROM group_members WHERE group_id = ? AND customer_id = ?");
    $checkMember->bind_param("ii", $group_id, $customer_id);
    $checkMember->execute();
    $memberResult = $checkMember->get_result();

    if ($memberResult->num_rows == 0) {
        $message = "You must join this bulk group before placing a bulk order.";
    } else {
        $checkGroup = $conn->prepare("SELECT target_quantity, ordered_quantity, status FROM bulk_groups WHERE group_id = ?");
        $checkGroup->bind_param("i", $group_id);
        $checkGroup->execute();
        $group = $checkGroup->get_result()->fetch_assoc();

        $remaining_quantity = $group["target_quantity"] - $group["ordered_quantity"];

        if ($group["status"] != "Open") {
            $message = "This bulk group is already completed or cancelled.";
        } elseif ($quantity > $remaining_quantity) {
            $message = "You cannot order more than the remaining target quantity. Remaining: " . $remaining_quantity;
        } else {
            $stmt = $conn->prepare("CALL place_order(?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiis", $customer_id, $product_id, $quantity, $group_id, $payment_method);

            if ($stmt->execute()) {
                $message = "Bulk order placed successfully with discount.";
            } else {
                $message = "Bulk order could not be placed.";
            }

            while ($conn->more_results() && $conn->next_result()) {}
        }
    }
}

$products = $conn->query("SELECT product_id, product_name FROM products WHERE stock_quantity > 0");

$groups = $conn->query("
    SELECT 
        bg.group_id, bg.product_id, p.product_name, p.price,
        bg.target_quantity, bg.ordered_quantity, bg.current_members,
        bg.discount_percentage, bg.status, c.full_name AS created_by,
        CASE WHEN gm.membership_id IS NOT NULL THEN 1 ELSE 0 END AS is_joined
    FROM bulk_groups bg
    JOIN products p ON bg.product_id = p.product_id
    JOIN customers c ON bg.created_by = c.customer_id
    LEFT JOIN group_members gm ON bg.group_id = gm.group_id AND gm.customer_id = $customer_id
    ORDER BY bg.group_id DESC
");
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Customer Portal</p>
        <h2 class="mb-0">Bulk Buying Groups</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
</div>

<?php if ($message != "") { ?>
    <div class="alert alert-info mt-3"><?php echo $message; ?></div>
<?php } ?>

<!-- Create Group Form -->
<div class="card p-4 mt-3 mb-4">
    <h4>Create New Bulk Group</h4>
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-3">
                <label>Product</label>
                <select name="product_id" class="form-control" required>
                    <?php while ($product = $products->fetch_assoc()) { ?>
                        <option value="<?php echo $product["product_id"]; ?>">
                            <?php echo $product["product_name"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Target Quantity</label>
                <input type="number" name="target_quantity" class="form-control" min="2" required>
            </div>
            <div class="col-md-3">
                <label>Discount %</label>
                <input type="number" step="0.01" name="discount_percentage" class="form-control" min="0" max="50" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" name="create_group" class="btn btn-success w-100">
                    <i class="bi bi-plus-lg me-1"></i> Create Group
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Groups Table -->
<h3 class="dash-section-title">Active Groups</h3>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Group</th>
                <th>Product</th>
                <th>Price</th>
                <th>Progress</th>
                <th>Members</th>
                <th>Discount</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Join</th>
                <th>Order</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $groups->fetch_assoc()) {
                $remaining = $row["target_quantity"] - $row["ordered_quantity"];
                $progress = $row["target_quantity"] > 0
                    ? round(($row["ordered_quantity"] / $row["target_quantity"]) * 100)
                    : 0;
                $sClass = strtolower($row["status"]) === "open" ? "status-confirmed" : "status-cancelled";
            ?>
                <tr>
                    <td><span style="color:var(--slate-dim);font-size:0.8rem;">#<?php echo $row["group_id"]; ?></span></td>
                    <td style="font-weight:600;"><?php echo $row["product_name"]; ?></td>
                    <td style="color:var(--emerald);font-weight:600;">Rs. <?php echo number_format($row["price"], 2); ?></td>
                    <td style="min-width:140px;">
                        <div class="bulk-progress">
                            <div class="bulk-progress-bar" style="width:<?php echo $progress; ?>%"></div>
                        </div>
                        <div style="font-size:0.72rem;color:var(--slate-dim);margin-top:4px;">
                            <?php echo $row["ordered_quantity"]; ?> / <?php echo $row["target_quantity"]; ?> &nbsp;·&nbsp; <?php echo $remaining; ?> left
                        </div>
                    </td>
                    <td><?php echo $row["current_members"]; ?></td>
                    <td style="color:var(--gold);font-weight:600;"><?php echo $row["discount_percentage"]; ?>%</td>
                    <td><span class="status-badge <?php echo $sClass; ?>"><?php echo $row["status"]; ?></span></td>
                    <td style="font-size:0.82rem;"><?php echo $row["created_by"]; ?></td>
                    <td>
                        <?php if ($row["is_joined"] == 1) { ?>
                            <span class="status-badge status-confirmed">Joined</span>
                        <?php } elseif ($row["status"] == "Open") { ?>
                            <a href="bulk_groups.php?join=<?php echo $row["group_id"]; ?>" class="btn btn-sm btn-success">
                                <i class="bi bi-person-plus"></i> Join
                            </a>
                        <?php } else { ?>
                            <span style="color:var(--slate-dim);font-size:0.8rem;">Closed</span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($row["is_joined"] == 1 && $row["status"] == "Open" && $remaining > 0) { ?>
                            <form method="POST">
                                <input type="hidden" name="group_id" value="<?php echo $row["group_id"]; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $row["product_id"]; ?>">
                                <div class="d-flex gap-1 align-items-center flex-wrap">
                                    <input type="number" name="quantity" min="1" max="<?php echo $remaining; ?>"
                                        class="form-control form-control-sm" style="width:70px;" placeholder="Qty" required>
                                    <select name="payment_method" class="form-control form-control-sm" style="width:90px;">
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Bank Transfer">Bank</option>
                                    </select>
                                    <button type="submit" name="order_from_group" class="btn btn-sm btn-warning">
                                        Order
                                    </button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <span style="color:var(--slate-dim);font-size:0.8rem;">—</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>