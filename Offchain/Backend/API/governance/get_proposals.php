<?php
require __DIR__ . "/../../config/db.php";

$status = $_GET["status"] ?? "";

$now = date("Y-m-d H:i:s");
$conn->query("UPDATE governance_proposals SET status = 'closed' WHERE status = 'active' AND deadline IS NOT NULL AND deadline < '$now'");

$query = "SELECT * FROM governance_proposals";
$params = [];
$types = "";

if ($status !== "") {
    $query .= " WHERE status = ?";
    $params[] = $status;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode(["proposals" => $rows]);
