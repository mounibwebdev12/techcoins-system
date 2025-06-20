<?php
// Connect to database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'techcoin';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current TechCoin balance
$result = $conn->query("SELECT coins FROM techcoin_wallet LIMIT 1");
$row = $result->fetch_assoc();
$coins = $row ? $row['coins'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Exchange TechCoins</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="techcoin.png" alt="TechCoin Logo" style="width: 80px; margin-bottom: 10px;">
      <h2>TechCoin</h2>
    </div>
    <a href="index.php">Wallet</a>
    <a href="buy.php">Buy TechCoins</a>
  </div>

  <div class="main">
    <h1>Exchange TechCoins</h1>
    <p><strong>Balance:</strong> <?= $coins >= 9999999 ? '∞' : $coins ?> TechCoin(s)</p>

    <form action="exchange_submit.php" method="POST">
      <label>How many TechCoins do you want to exchange?</label><br>
      <input type="number" name="amount" min="1" max="<?= $coins ?>" required><br><br>

      <label>Choose currency:</label><br>
      <select name="currency" required>
        <option value="USD">USD</option>
        <option value="DZD">DZD</option>
      </select><br><br>

      <label>Your PayPal email (for receiving money):</label><br>
      <input type="email" name="paypal_email" required><br><br>

      <button type="submit">Exchange</button>
    </form>
  </div>
</body>
</html>
