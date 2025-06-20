<?php
include 'database.php';

$result = $conn->query("SELECT coins FROM techcoin_wallet LIMIT 1");
$row = $result->fetch_assoc();
$coins = $row ? $row['coins'] : 0;

$usd = $coins * 100;
$dzd = $coins * 13005.91;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>TechCoin Wallet</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="techcoin.png" alt="TechCoin Logo" style="width: 80px; margin-bottom: 10px;">
      <h2>TechCoin</h2>
    </div>
    <a href="buy.php">Buy TechCoins</a>
    <a href="exchange.php">Exchange TechCoins</a>
  </div>

  <div class="main">
    <h1>Your Wallet</h1>
    <p><strong>Balance:</strong> <?= $coins >= 9999999 ? '∞' : $coins ?> TechCoin(s)</p>
    <p><strong>USD:</strong> $<?= number_format($usd, 2) ?></p>
    <p><strong>DZD:</strong> <?= number_format($dzd, 2) ?> DZD</p>
  </div>
</body>
</html>
