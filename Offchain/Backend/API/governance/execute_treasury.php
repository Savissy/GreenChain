<?php
require __DIR__ . "/../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$proposalId = (int) ($data["proposal_id"] ?? 0);
$executorWallet = trim($data["executor_wallet"] ?? "");
$action = trim($data["action"] ?? "");
$txHash = trim($data["tx_hash"] ?? "");

if (!$proposalId || $executorWallet === "" || $action === "") {
    http_response_code(422);
    echo json_encode(["error" => "Missing execution details"]);
    exit;
}

$stmt = $conn->prepare("SELECT status, yes_weight, quorum FROM governance_proposals WHERE id = ?");
$stmt->bind_param("i", $proposalId);
$stmt->execute();
$proposal = $stmt->get_result()->fetch_assoc();

if (!$proposal || $proposal["status"] !== "closed") {
    http_response_code(409);
    echo json_encode(["error" => "Proposal not ready for execution"]);
    exit;
}

if ((int) $proposal["yes_weight"] < (int) $proposal["quorum"]) {
    http_response_code(403);
    echo json_encode(["error" => "Quorum not met"]);
    exit;
}

$execStmt = $conn->prepare("
  INSERT INTO governance_executions
  (proposal_id, executor_wallet, action, tx_hash)
  VALUES (?, ?, ?, ?)
");
$execStmt->bind_param("isss", $proposalId, $executorWallet, $action, $txHash);
$execStmt->execute();

$update = $conn->prepare("UPDATE governance_proposals SET status = 'executed', executed_at = NOW() WHERE id = ?");
$update->bind_param("i", $proposalId);
$update->execute();

echo json_encode(["success" => true]);
