<?php
include "../includes/config.php";
include "../includes/functions.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = sanitize($_POST["full_name"]);
    $email = sanitize($_POST["email"]);
    $password = $_POST["password"];
    $phone = sanitize($_POST["phone"]);
    $address = sanitize($_POST["address"]);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT customer_id FROM customers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "Email already exists.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO customers (full_name, email, password, phone, address)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $full_name, $email, $hashed_password, $phone, $address);

        if ($stmt->execute()) {
            $message = "Registration successful. You can now login.";
        } else {
            $message = "Registration failed.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="auth-card">

            <div class="auth-visual">
                <div class="auth-visual-top">
                    <span class="auth-kicker">Member Access</span>
                    <h2>Buy smarter,<br>waste less.</h2>
                    <p class="lead-text">Join thousands of households already saving through bulk buying.</p>

                    <ul class="auth-benefits">
                        <li>
                            <i class="bi bi-tag-fill"></i>
                            <div><strong>Lower prices</strong>Unlock group discounts on every order</div>
                        </li>
                        <li>
                            <i class="bi bi-people-fill"></i>
                            <div><strong>Group buying</strong>Create or join bulk orders with your community</div>
                        </li>
                        <li>
                            <i class="bi bi-arrow-repeat"></i>
                            <div><strong>Less packaging</strong>Fewer deliveries, smaller footprint</div>
                        </li>
                    </ul>
                </div>

                <div class="stat-strip">
                    <div><strong>4.2k+</strong><span>Members</span></div>
                    <div><strong>380+</strong><span>Eco products</span></div>
                    <div><strong>92%</strong><span>Avg savings</span></div>
                </div>
            </div>

            <div class="auth-form-panel">
                <h3>Create your account</h3>
                <p class="auth-subtext">It only takes a minute.</p>

                <?php if ($message != "") { ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <div class="input-icon">
                            <i class="bi bi-person"></i>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <div class="input-icon">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <div class="input-icon">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Phone</label>
                        <div class="input-icon">
                            <i class="bi bi-telephone"></i>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Create Account</button>
                </form>

                <p class="mt-3 text-center">
                    Already have an account?
                    <a href="login.php">Login here</a>
                </p>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>