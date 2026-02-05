<?php
session_start();
require __DIR__ . "/../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data["title"] ?? "");
$summary = trim($data["summary"] ?? "");
$proposalHash = trim($data["proposal_hash"] ?? "");
$proposerWallet = trim($data["proposer_wallet"] ?? "");
$deadline = $data["deadline"] ?? null;
$activate = (bool) ($data["activate"] ?? true);
$quorum = (int) ($data["quorum"] ?? 1);

if ($title === "" || $proposalHash === "" || $proposerWallet === "") {
    http_response_code(422);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

$status = $activate ? "active" : "draft";

$stmt = $conn->prepare("
  INSERT INTO governance_proposals
  (title, summary, proposal_hash, proposer_wallet, status, deadline, quorum)
  VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "ssssssi",
  $title,
  $summary,
  $proposalHash,
  $proposerWallet,
  $status,
  $deadline,
  $quorum
);

$stmt->execute();
$proposalId = $stmt->insert_id;

if ($activate) {
    $transition = $conn->prepare("
      UPDATE governance_proposals
      SET status = 'active'
      WHERE id = ? AND status = 'draft'
    ");
    $transition->bind_param("i", $proposalId);
    $transition->execute();
}

echo json_encode([
  "success" => true,
  "proposal_id" => $proposalId,
  "status" => $status
]);
