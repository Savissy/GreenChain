<?php
require __DIR__ . "/../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$wallet = trim($data["wallet_address"] ?? "");
$nftPolicyId = trim($data["nft_policy_id"] ?? "");
$nftAssetName = trim($data["nft_asset_name"] ?? "");
$weight = (int) ($data["weight"] ?? 1);

if ($wallet === "" || $nftPolicyId === "" || $nftAssetName === "") {
    http_response_code(422);
    echo json_encode(["error" => "Missing minting details"]);
    exit;
}

$stmt = $conn->prepare("
  INSERT INTO governance_members
  (wallet_address, nft_policy_id, nft_asset_name, weight)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    nft_policy_id = VALUES(nft_policy_id),
    nft_asset_name = VALUES(nft_asset_name),
    weight = VALUES(weight),
    burned_at = NULL,
    minted_at = NOW()
");
$stmt->bind_param("sssi", $wallet, $nftPolicyId, $nftAssetName, $weight);
$stmt->execute();

echo json_encode(["success" => true]);
