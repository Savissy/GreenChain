<?php
require __DIR__ . "/../../config/db.php";

$proposalQuery = "SELECT * FROM governance_proposals ORDER BY created_at DESC";
$proposalResult = $conn->query($proposalQuery);
$proposals = [];

while ($row = $proposalResult->fetch_assoc()) {
    $proposals[] = $row;
}

$voteQuery = "SELECT proposal_id, wallet_address, choice, weight, message, signature, created_at FROM governance_votes ORDER BY created_at DESC";
$voteResult = $conn->query($voteQuery);
$votes = [];

while ($row = $voteResult->fetch_assoc()) {
    $votes[] = $row;
}

echo json_encode([
  "proposals" => $proposals,
  "votes" => $votes
]);
