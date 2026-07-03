<?php
include "../includes/config.php";
include "../includes/functions.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST["email"]);
    $password = $_POST["password"];
    $role = sanitize($_POST["role"]);

    if ($role === "admin") {
        $stmt = $conn->prepare("SELECT admin_id, full_name, password FROM administrators WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();

            if ($password === $admin["password"] || password_verify($password, $admin["password"])) {
                $_SESSION["user_id"] = $admin["admin_id"];
                $_SESSION["full_name"] = $admin["full_name"];
                $_SESSION["role"] = "admin";
                redirect("../admin/dashboard.php");
            } else {
                $message = "Invalid admin password.";
            }
        } else {
            $message = "Admin account not found.";
        }
    }

    if ($role === "customer") {
        $stmt = $conn->prepare("SELECT customer_id, full_name, password FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $customer = $result->fetch_assoc();

            if ($password === $customer["password"] || password_verify($password, $customer["password"])) {
                $_SESSION["user_id"] = $customer["customer_id"];
                $_SESSION["full_name"] = $customer["full_name"];
                $_SESSION["role"] = "customer";
                redirect("../customer/dashboard.php");
            } else {
                $message = "Invalid customer password.";
            }
        } else {
            $message = "Customer account not found.";
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
                    <h2>Welcome back.</h2>
                    <p class="lead-text">Log in to manage your orders, track your bulk buying groups, and keep saving sustainably.</p>

                    <ul class="auth-benefits">
                        <li>
                            <i class="bi bi-box-seam"></i>
                            <div><strong>Browse eco products</strong>Certified sustainable items, ready to order</div>
                        </li>
                        <li>
                            <i class="bi bi-people-fill"></i>
                            <div><strong>Join bulk groups</strong>Hop into active group orders instantly</div>
                        </li>
                        <li>
                            <i class="bi bi-truck"></i>
                            <div><strong>Track orders</strong>Real-time status from purchase to delivery</div>
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
                <h3>Log in</h3>
                <p class="auth-subtext">Welcome back, we missed you.</p>

                <?php if ($message != "") { ?>
                    <div class="alert alert-danger"><?php echo $message; ?></div>
                <?php } ?>

                <form method="POST">
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
                        <label>Login As</label>
                        <select name="role" class="form-control" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>

                <p class="mt-3 text-center">
                    New customer?
                    <a href="register.php">Register here</a>
                </p>

                <div class="alert alert-secondary mt-3">
                    <strong>Admin Demo:</strong><br>
                    Email: admin@ecobulk.com<br>
                    Password: admin123
                </div>
            </div>

        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>