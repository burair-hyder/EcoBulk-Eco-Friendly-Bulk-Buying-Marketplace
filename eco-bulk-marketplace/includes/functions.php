<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

function isCustomer() {
    return isLoggedIn() && $_SESSION['role'] === 'customer';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(trim($data));
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect("../auth/login.php");
    }
}

function requireCustomer() {
    if (!isCustomer()) {
        redirect("../auth/login.php");
    }
}
?>