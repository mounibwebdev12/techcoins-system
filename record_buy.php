<?php
// Connect to your database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'techcoin';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed";
    exit;
}

// Get current coin balance
$result = $conn->query("SELECT coins FROM techcoin_wallet LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $new_balance = $row['coins'] + 1;
    $conn->query("UPDATE techcoin_wallet SET coins = $new_balance");
} else {
    // First ever insert
    $conn->query("INSERT INTO techcoin_wallet (coins) VALUES (1)");
}

echo "success";
?>
