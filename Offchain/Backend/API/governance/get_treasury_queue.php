<?php
require __DIR__ . "/../../config/db.php";

$now = date("Y-m-d H:i:s");
$conn->query("UPDATE governance_proposals SET status = 'closed' WHERE status = 'active' AND deadline IS NOT NULL AND deadline < '$now'");

$query = "SELECT * FROM governance_proposals WHERE status = 'closed' AND yes_weight >= quorum ORDER BY created_at DESC";
$result = $conn->query($query);
$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode(["approved" => $rows]);
