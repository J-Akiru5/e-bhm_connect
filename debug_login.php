<?php
/**
 * Debug script to diagnose login issues
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

session_start();

echo "<h2>Session Debug</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Request Debug</h2>";
echo "<pre>";
echo "GET: ";
print_r($_GET);
echo "POST: ";
print_r($_POST);
echo "</pre>";

echo "<h2>User Accounts Check</h2>";
try {
    $stmt = $pdo->query("SELECT bhw_id, full_name, username, email_verified, account_status, role FROM bhw_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Username</th><th>Email Verified</th><th>Account Status</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['bhw_id']}</td>";
        echo "<td>{$user['full_name']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email_verified']}</td>";
        echo "<td>{$user['account_status']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Throwable $e) {
    echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Login Test</h2>";
echo "<form method='POST' action='" . BASE_URL . "?action=login-bhw'>";
echo "<input type='hidden' name='csrf_token' value='" . ($_SESSION['csrf_token'] ?? '') . "'>";
echo "<label>Username: <input type='text' name='username'></label><br><br>";
echo "<label>Password: <input type='password' name='password'></label><br><br>";
echo "<button type='submit'>Test Login</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='" . BASE_URL . "login-bhw'>Go to Normal Login Page</a></p>";
echo "<p><a href='" . BASE_URL . "admin-dashboard'>Try Dashboard Directly</a></p>";
