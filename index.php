<!-- index.php -->
<?php
include 'database.php';
$result = $conn->query("SELECT techcoins FROM users WHERE id = 1");
$row = $result->fetch_assoc();
$coins = $row['techcoins'];
$usd = $coins * 100;
$dzd = $coins * 13005.91;
?>
<!DOCTYPE html>
<html>
<head>
  <title>TechCoin Wallet</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <h2>TechCoin</h2>
    <a href="buy.php">Buy TechCoins</a>
    <a href="exchange.php">Exchange TechCoins</a>
  </div>
  <div class="main">
    <h1>Wallet</h1>
    <p><strong>Balance:</strong> <?= $coins == 9999999 ? '∞' : $coins ?> TechCoin(s)</p>
    <p><strong>USD:</strong> $<?= number_format($usd, 2) ?></p>
    <p><strong>DZD:</strong> <?= number_format($dzd, 2) ?> DZD</p>
  </div>
</body>
</html>

<!-- buy.php -->
<?php include 'paypal_config.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Buy TechCoins</title>
  <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id ?>&currency=USD"></script>
</head>
<body>
  <h1>Buy TechCoins</h1>
  <div id="paypal-button-container"></div>
  <script>
    paypal.Buttons({
      createOrder: function(data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: '100.00' // 1 TechCoin = $100
            }
          }]
        });
      },
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
          alert('Payment successful. You bought 1 TechCoin.');
          window.location.href = "record_buy.php";
        });
      }
    }).render('#paypal-button-container');
  </script>
</body>
</html>

<!-- record_buy.php -->
<?php
include 'database.php';
$conn->query("UPDATE users SET techcoins = techcoins + 1 WHERE id = 1");
$conn->query("INSERT INTO transactions (type, amount, currency, status) VALUES ('buy', 1, 'USD', 'completed')");
header("Location: index.php");
?>

<!-- exchange.php -->
<!DOCTYPE html>
<html>
<head><title>Exchange TechCoins</title></head>
<body>
  <h1>Exchange TechCoins</h1>
  <form action="exchange_submit.php" method="POST">
    <label>Amount of TechCoins:</label><br>
    <input type="number" name="amount" min="1" required><br><br>
    <label>Select currency:</label><br>
    <select name="currency">
      <option value="USD">USD</option>
      <option value="DZD">DZD</option>
    </select><br><br>
    <label>Your PayPal email:</label><br>
    <input type="email" name="paypal_email" required><br><br>
    <button type="submit">Exchange</button>
  </form>
</body>
</html>

<!-- exchange_submit.php -->
<?php
include 'database.php';
$amount = (int)$_POST['amount'];
$currency = $_POST['currency'];
$email = $_POST['paypal_email'];

$result = $conn->query("SELECT techcoins FROM users WHERE id = 1");
$row = $result->fetch_assoc();
$balance = $row['techcoins'];

if ($amount > 0 && $amount <= $balance) {
    $conn->query("UPDATE users SET techcoins = techcoins - $amount WHERE id = 1");
    $conn->query("INSERT INTO transactions (type, amount, currency, status) VALUES ('exchange', $amount, '$currency', 'pending')");
    echo "✅ Exchange request recorded. You will receive $currency soon.";
} else {
    echo "❌ Not enough TechCoins.";
}
?>
<br><a href="index.php">Back</a>

<!-- admin.php -->
<?php
include 'database.php';
$result = $conn->query("SELECT * FROM transactions ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Admin Panel</title></head>
<body>
  <h1>Transactions Log</h1>
  <table border="1">
    <tr><th>ID</th><th>Type</th><th>Amount</th><th>Currency</th><th>Status</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['type'] ?></td>
      <td><?= $row['amount'] ?></td>
      <td><?= $row['currency'] ?></td>
      <td><?= $row['status'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

<!-- paypal_config.php -->
<?php
$paypal_client_id = "AcKNQugfH6LQ7ApKcATgXGjvRcnZgCwg17pJeHVI4Iu2RXLzDrl5c7q22zYY-sVtr417SPImLGCjnqYN";
$paypal_secret = EAbCP8nqoRoS6YDEF2yXUwG_qS9_jG23_5j1RJbpWjZjzIcTP8x1nXzNUjIN2HwyPNaNMDZ0B7mIRaBm"";
?>

<!-- database.php -->
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "techcoin";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!-- style.css -->
body {
  display: flex;
  margin: 0;
  font-family: sans-serif;
}
.sidebar {
  width: 200px;
  background: #111;
  color: white;
  padding: 20px;
  height: 100vh;
}
.sidebar a {
  display: block;
  color: white;
  text-decoration: none;
  margin: 10px 0;
}
.main {
  flex: 1;
  padding: 40px;
}

<!-- SQL file (run in phpMyAdmin) -->
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50),
  techcoins INT DEFAULT 0
);

INSERT INTO users (name, techcoins) VALUES ('Admin', 9999999);

CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(20),
  amount INT,
  currency VARCHAR(10),
  status VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

