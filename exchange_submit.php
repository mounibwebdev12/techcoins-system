<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'techcoin';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$amount = intval($_POST['amount']);
$currency = $_POST['currency'];
$email = $_POST['paypal_email'];

// Get current balance
$result = $conn->query("SELECT coins FROM techcoin_wallet LIMIT 1");
$row = $result->fetch_assoc();
$current_coins = $row ? $row['coins'] : 0;

// Check if enough coins are available
if ($amount > $current_coins && $current_coins < 9999999) {
    die("Not enough TechCoins.");
}

// Deduct coins if not infinite
if ($current_coins < 9999999) {
    $new_balance = $current_coins - $amount;
    $conn->query("UPDATE techcoin_wallet SET coins = $new_balance");
}

// Log the exchange request
$conn->query("CREATE TABLE IF NOT EXISTS exchange_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coins INT,
  currency VARCHAR(10),
  email VARCHAR(255),
  status VARCHAR(20) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$stmt = $conn->prepare("INSERT INTO exchange_requests (coins, currency, email) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $amount, $currency, $email);
$stmt->execute();

echo "<script>alert('Exchange request submitted. You will receive the payment soon.'); window.location.href='index.php';</script>";
?>
