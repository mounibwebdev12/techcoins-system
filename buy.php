<?php
// DIRECTLY SET YOUR PAYPAL CREDENTIALS HERE:
$paypal_client_id = "YOUR_CLIENT_ID_HERE";  // 👈 Replace this
$paypal_secret = "YOUR_SECRET_ID_HERE";     // 👈 Replace this

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'techcoin';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buy TechCoins</title>

  <!-- 💸 PayPal SDK using YOUR client ID -->
  <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id ?>&currency=USD"></script>

  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="techcoin.png" alt="TechCoin Logo" style="width: 80px; margin-bottom: 10px;">
      <h2>TechCoin</h2>
    </div>
    <a href="index.php">Wallet</a>
    <a href="exchange.php">Exchange TechCoins</a>
  </div>

  <div class="main">
    <h1>Buy TechCoins</h1>
    <p>1 TechCoin = <strong>$100</strong></p>

    <div id="paypal-button-container"></div>

    <script>
      paypal.Buttons({
        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{
              amount: { value: '100.00' }
            }]
          });
        },
        onApprove: function(data, actions) {
          return actions.order.capture().then(function(details) {
            // Tell backend to add 1 TechCoin
            fetch('record_buy.php', {
              method: 'POST'
            }).then(res => {
              if (res.ok) {
                alert('✅ 1 TechCoin added!');
                window.location.href = 'index.php';
              } else {
                alert('❌ Error updating balance.');
              }
            });
          });
        }
      }).render('#paypal-button-container');
    </script>
  </div>
</body>
</html>
