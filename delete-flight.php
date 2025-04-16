<?php
include 'includes/auth.php';
include 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid flight ID.";
    exit();
}

$stmt = $conn->prepare("DELETE FROM flights WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: view-flights.php");
    exit();
} else {
    echo "Failed to delete flight.";
}
?>
