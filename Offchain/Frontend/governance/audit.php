<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GreenChain DAO - Audit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../Assets/css/dao.css">
  <script type="module" src="../Assets/JS/dao/dao-audit.js" defer></script>
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
    <h1>Audit & Transparency</h1>

    <h2 class="section-title">Proposal Ledger</h2>
    <div id="proposalAudit" class="dao-grid"></div>

    <h2 class="section-title">Vote Ledger</h2>
    <div id="voteAudit" class="dao-grid"></div>
  </main>
</body>
</html>
