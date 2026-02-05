<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GreenChain DAO - Voting</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Assets/css/dao.css">
  <script type="module" src="../Assets/JS/dao/dao-voting.js" defer></script>
</head>
<body>
  <nav class="navbar">
    <div class="logo">🌱 GreenChain DAO</div>
    <div>
      <a href="index.php">Landing</a>
      <a href="proposals.php">Proposals</a>
      <a href="voting.php">Voting</a>
      <a href="treasury.php">Treasury</a>
      <a href="audit.php">Audit</a>
    </div>
  </nav>

  <main class="dao-container">
    <h1>Active Voting</h1>
    <div class="wallet-row">
      <select id="walletSelect">
        <option value="">Select wallet</option>
      </select>
      <button id="connectWallet">Connect Wallet</button>
      <div class="wallet-status" id="walletStatus">Not connected</div>
    </div>

    <p id="membershipStatus" class="wallet-status">Checking membership...</p>

    <div id="voteList" class="dao-grid"></div>
  </main>
</body>
</html>
