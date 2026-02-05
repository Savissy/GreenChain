<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GreenChain DAO - Proposals</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Assets/css/dao.css">
  <script type="module" src="../Assets/JS/dao/dao-proposals.js" defer></script>
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
    <h1>Submit a Proposal</h1>
    <div class="wallet-row">
      <select id="walletSelect">
        <option value="">Select wallet</option>
      </select>
      <button id="connectWallet">Connect Wallet</button>
      <div class="wallet-status" id="walletStatus">Not connected</div>
    </div>

    <form id="proposalForm" class="dao-card">
      <label for="proposalTitle">Title</label>
      <input type="text" id="proposalTitle" required>

      <label for="proposalSummary">Summary</label>
      <textarea id="proposalSummary" rows="3"></textarea>

      <label for="proposalDetails">Details (hashed locally)</label>
      <textarea id="proposalDetails" rows="6" required></textarea>

      <label for="proposalDeadline">Voting Deadline</label>
      <input type="datetime-local" id="proposalDeadline">

      <label for="proposalQuorum">Quorum (min yes weight)</label>
      <input type="number" id="proposalQuorum" min="1" value="1">

      <button type="submit">Submit Proposal</button>
    </form>

    <h2 class="section-title">Recent Proposals</h2>
    <div id="proposalList" class="dao-grid"></div>
  </main>
</body>
</html>
