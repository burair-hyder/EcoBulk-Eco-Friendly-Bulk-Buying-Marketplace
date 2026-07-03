<?php
include "../includes/config.php";
include "../includes/functions.php";
requireCustomer();

$message = "";
$customer_id = $_SESSION["user_id"];

if (isset($_POST["update_profile"])) {
    $full_name = sanitize($_POST["full_name"]);
    $phone = sanitize($_POST["phone"]);
    $address = sanitize($_POST["address"]);

    $stmt = $conn->prepare("
        UPDATE customers 
        SET full_name = ?, phone = ?, address = ?
        WHERE customer_id = ?
    ");
    $stmt->bind_param("sssi", $full_name, $phone, $address, $customer_id);

    if ($stmt->execute()) {
        $_SESSION["full_name"] = $full_name;
        $message = "Profile updated successfully.";
    } else {
        $message = "Profile could not be updated.";
    }
}

$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
?>

<?php include "../includes/header.php"; ?>

<div class="dash-header">
    <div>
        <p class="dash-kicker">Customer Portal</p>
        <h2 class="mb-0">My Profile</h2>
    </div>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
</div>

<?php if ($message != "") { ?>
    <div class="alert alert-success mt-3"><?php echo $message; ?></div>
<?php } ?>

<div class="row g-4 mt-1">

    <!-- Avatar / Info Panel -->
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($customer["full_name"], 0, 1)); ?>
            </div>
            <h4 class="mt-3 mb-1"><?php echo $customer["full_name"]; ?></h4>
            <p style="font-size:0.85rem;"><?php echo $customer["email"]; ?></p>

            <div class="profile-info-row">
                <i class="bi bi-telephone"></i>
                <span><?php echo $customer["phone"] ?: "Not provided"; ?></span>
            </div>
            <div class="profile-info-row">
                <i class="bi bi-geo-alt"></i>
                <span><?php echo $customer["address"] ?: "Not provided"; ?></span>
            </div>
            <div class="profile-info-row">
                <i class="bi bi-calendar3"></i>
                <span>Joined <?php echo date("d M Y", strtotime($customer["created_at"])); ?></span>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-lg-8">
        <div class="card p-4">
            <h4>Edit Information</h4>

            <form method="POST">
                <div class="mb-3">
                    <label>Full Name</label>
                    <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" name="full_name" class="form-control"
                               value="<?php echo $customer["full_name"]; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Email <span style="color:var(--slate-dim);font-weight:400;">(cannot be changed)</span></label>
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="form-control"
                               value="<?php echo $customer["email"]; ?>" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Phone</label>
                    <div class="input-icon">
                        <i class="bi bi-telephone"></i>
                        <input type="text" name="phone" class="form-control"
                               value="<?php echo $customer["phone"]; ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?php echo $customer["address"]; ?></textarea>
                </div>

                <button type="submit" name="update_profile" class="btn btn-success">
                    <i class="bi bi-check-lg me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>