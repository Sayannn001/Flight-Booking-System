<?php
include 'includes/auth.php';
include 'includes/db.php';

// Handle filters
$where = "1";
$params = [];
$types = "";

if (!empty($_GET['source'])) {
    $where .= " AND source = ?";
    $params[] = $_GET['source'];
    $types .= "s";
}
if (!empty($_GET['destination'])) {
    $where .= " AND destination = ?";
    $params[] = $_GET['destination'];
    $types .= "s";
}
if (!empty($_GET['date'])) {
    $where .= " AND date = ?";
    $params[] = $_GET['date'];
    $types .= "s";
}

$sql = "SELECT * FROM flights WHERE $where ORDER BY date DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Flights</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef1f5;
            padding: 30px;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 1000px;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        input[type="text"], input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 20px;
            border: none;
            background: #007BFF;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 10px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .actions i {
            margin: 0 8px;
            cursor: pointer;
            color: #007BFF;
        }
        .actions i:hover {
            color: #ff4747;
        }
        .home-button {
            padding: 10px 16px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
<div class="container">
    <a class="home-button" href="dashboard.php">🏠 Home</a>
    <h2>View Flights</h2>

    <form method="get">
        <input type="text" name="source" placeholder="Source" value="<?= $_GET['source'] ?? '' ?>">
        <input type="text" name="destination" placeholder="Destination" value="<?= $_GET['destination'] ?? '' ?>">
        <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>">
        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Flight No.</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Date</th>
            <th>Time</th>
            <th>Total Seats</th>
            <th>Actions</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['flight_number'] ?></td>
                    <td><?= $row['source'] ?></td>
                    <td><?= $row['destination'] ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['time'] ?></td>
                    <td><?= $row['total_seats'] ?></td>
                    <td class="actions">
                        <a href="edit-flight.php?id=<?= $row['id'] ?>"><i class="fas fa-edit"></i></a>
                        <a href="delete-flight.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this flight?')"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No flights found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
