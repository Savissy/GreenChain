<?php
require __DIR__ . "/../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$proposalId = (int) ($data["proposal_id"] ?? 0);
$wallet = trim($data["wallet_address"] ?? "");
$choice = trim($data["choice"] ?? "");
$message = trim($data["message"] ?? "");
$signature = json_encode($data["signature"] ?? []);

if (!$proposalId || $wallet === "" || !in_array($choice, ["yes", "no", "abstain"], true)) {
    http_response_code(422);
    echo json_encode(["error" => "Invalid vote payload"]);
    exit;
}

$proposalStmt = $conn->prepare("SELECT status, deadline FROM governance_proposals WHERE id = ?");
$proposalStmt->bind_param("i", $proposalId);
$proposalStmt->execute();
$proposal = $proposalStmt->get_result()->fetch_assoc();

if (!$proposal || $proposal["status"] !== "active") {
    http_response_code(409);
    echo json_encode(["error" => "Proposal not active"]);
    exit;
}

if ($proposal["deadline"] && strtotime($proposal["deadline"]) < time()) {
    http_response_code(409);
    echo json_encode(["error" => "Voting deadline passed"]);
    exit;
}

$memberStmt = $conn->prepare("SELECT weight FROM governance_members WHERE wallet_address = ? AND burned_at IS NULL");
$memberStmt->bind_param("s", $wallet);
$memberStmt->execute();
$member = $memberStmt->get_result()->fetch_assoc();

if (!$member) {
    http_response_code(403);
    echo json_encode(["error" => "Wallet is not a governance member"]);
    exit;
}

$weight = (int) ($member["weight"] ?? 1);
if ($weight < 1) {
    $weight = 1;
}

$stmt = $conn->prepare("
  INSERT INTO governance_votes
  (proposal_id, wallet_address, choice, weight, message, signature)
  VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("ississ", $proposalId, $wallet, $choice, $weight, $message, $signature);

if (!$stmt->execute()) {
    http_response_code(409);
    echo json_encode(["error" => "Vote already submitted"]);
    exit;
}

if ($choice === "yes") {
    $conn->query("UPDATE governance_proposals SET yes_weight = yes_weight + $weight WHERE id = $proposalId");
} elseif ($choice === "no") {
    $conn->query("UPDATE governance_proposals SET no_weight = no_weight + $weight WHERE id = $proposalId");
} else {
    $conn->query("UPDATE governance_proposals SET abstain_weight = abstain_weight + $weight WHERE id = $proposalId");
}

echo json_encode(["success" => true, "weight" => $weight]);
