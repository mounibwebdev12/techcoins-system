<?php
include 'database.php';
include 'paypal_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buy TechCoins</title>
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
    <p>Price: <strong>$100</strong> per TechCoin</p>

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
            // Send request to record the purchase
            fetch('record_buy.php', {
              method: 'POST'
