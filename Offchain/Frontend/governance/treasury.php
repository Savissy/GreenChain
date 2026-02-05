<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GreenChain DAO - Treasury</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Assets/css/dao.css">
  <script type="module" src="../Assets/JS/dao/dao-treasury.js" defer></script>
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
    <h1>Treasury & Admin Dashboard</h1>
    <div class="wallet-row">
      <select id="walletSelect">
        <option value="">Select wallet</option>
      </select>
      <button id="connectWallet">Connect Wallet</button>
      <div class="wallet-status" id="walletStatus">Not connected</div>
    </div>

    <h2 class="section-title">Approved Proposals</h2>
    <div id="treasuryList" class="dao-grid"></div>

    <h2 class="section-title">Mint Governance NFT</h2>
    <form id="mintForm" class="dao-card">
      <input type="text" id="mintWallet" placeholder="Wallet address" required>
      <input type="text" id="mintPolicy" placeholder="NFT policy ID" required>
      <input type="text" id="mintAsset" placeholder="NFT asset name" required>
      <input type="number" id="mintWeight" placeholder="Voting weight" min="1" value="1">
      <button type="submit">Mint Member NFT</button>
    </form>

    <h2 class="section-title">Burn Governance NFT</h2>
    <form id="burnForm" class="dao-card">
      <input type="text" id="burnWallet" placeholder="Wallet address" required>
      <button type="submit">Burn Membership</button>
    </form>
  </main>
</body>
</html>
