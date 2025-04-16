<?php
include 'includes/auth.php';
include 'includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid flight ID.";
    exit();
}

// Fetch existing flight details
$stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE flights SET flight_number = ?, source = ?, destination = ?, date = ?, time = ?, total_seats = ? WHERE id = ?");
    $stmt->bind_param("sssssii", $_POST["flight_number"], $_POST["source"], $_POST["destination"], $_POST["date"], $_POST["time"], $_POST["total_seats"], $id);
    if ($stmt->execute()) {
        header("Location: view-flights.php");
        exit();
    } else {
        echo "Failed to update flight.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Flight</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f5;
            padding: 30px;
        }
        form {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
        }
        input, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <form method="post">
        <h2>Edit Flight</h2>
        <input name="flight_number" value="<?= $flight['flight_number'] ?>" required>
        <input name="source" value="<?= $flight['source'] ?>" required>
        <input name="destination" value="<?= $flight['destination'] ?>" required>
        <input type="date" name="date" value="<?= $flight['date'] ?>" required>
        <input type="time" name="time" value="<?= $flight['time'] ?>" required>
        <input type="number" name="total_seats" value="<?= $flight['total_seats'] ?>" required>
        <button type="submit">Update Flight</button>
    </form>
</body>
</html>
