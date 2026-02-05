<?php
require __DIR__ . "/../../config/db.php";

$wallet = trim($_GET["wallet"] ?? "");

if ($wallet === "") {
    http_response_code(422);
    echo json_encode(["error" => "Wallet required"]);
    exit;
}

$stmt = $conn->prepare("SELECT wallet_address, weight, nft_policy_id, nft_asset_name, minted_at FROM governance_members WHERE wallet_address = ? AND burned_at IS NULL");
$stmt->bind_param("s", $wallet);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) {
    echo json_encode(["is_member" => false]);
    exit;
}

echo json_encode([
  "is_member" => true,
  "member" => $member
]);
