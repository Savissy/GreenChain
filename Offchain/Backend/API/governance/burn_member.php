<?php
require __DIR__ . "/../../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$wallet = trim($data["wallet_address"] ?? "");

if ($wallet === "") {
    http_response_code(422);
    echo json_encode(["error" => "Wallet required"]);
    exit;
}

$stmt = $conn->prepare("UPDATE governance_members SET burned_at = NOW() WHERE wallet_address = ?");
$stmt->bind_param("s", $wallet);
$stmt->execute();

echo json_encode(["success" => true]);
