<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GreenChain DAO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Assets/css/dao.css">
  <script type="module" src="../Assets/JS/dao/dao-landing.js" defer></script>
</head>
<body>
  <nav class="navbar">
    <div class="logo">🌱 GreenChain DAO</div>
    <div>
      <a href="../index.php">Home</a>
      <a href="proposals.php">Proposals</a>
      <a href="voting.php">Voting</a>
      <a href="treasury.php">Treasury</a>
      <a href="audit.php">Audit</a>
    </div>
  </nav>

  <section class="dao-hero">
    <div class="dao-container">
      <h1>Community Governance</h1>
      <p>
        GreenChain is governed by NFT-backed members. Submit proposals, vote with weighted
        governance NFTs, and execute treasury actions after quorum approval.
      </p>
      <div class="wallet-row">
        <select id="walletSelect">
          <option value="">Select wallet</option>
        </select>
        <button id="connectWallet">Connect Wallet</button>
        <div class="wallet-status" id="walletStatus">Not connected</div>
      </div>
    </div>
  </section>

  <main class="dao-container">
    <h2 class="section-title">Governance Flow</h2>
    <div class="dao-grid">
      <div class="dao-card">
        <h3>Submit Proposals</h3>
        <p>Hash proposal content locally and register it on-chain.</p>
      </div>
      <div class="dao-card">
        <h3>NFT-Gated Voting</h3>
        <p>Vote once per wallet with weighted governance NFTs.</p>
      </div>
      <div class="dao-card">
        <h3>Treasury Execution</h3>
        <p>Execute approved proposals with DAO and multi-sig checks.</p>
      </div>
      <div class="dao-card">
        <h3>Audit & Transparency</h3>
        <p>Review tallies, signatures, and proposal hashes anytime.</p>
      </div>
    </div>
  </main>
</body>
</html>
