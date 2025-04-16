<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}
include 'includes/db.php';

// Handle cancellation
if (isset($_GET['cancel'])) {
    $booking_id = intval($_GET['cancel']);
    $conn->query("DELETE FROM passengers WHERE booking_id = $booking_id");
    $conn->query("DELETE FROM bookings WHERE id = $booking_id");
    header("Location: view-bookings.php");
    exit();
}

// Filters
$filter_sql = "";
$params = [];
$types = "";

if (!empty($_GET['source'])) {
    $filter_sql .= " AND f.source = ?";
    $params[] = $_GET['source'];
    $types .= "s";
}
if (!empty($_GET['destination'])) {
    $filter_sql .= " AND f.destination = ?";
    $params[] = $_GET['destination'];
    $types .= "s";
}
if (!empty($_GET['date'])) {
    $filter_sql .= " AND f.date = ?";
    $params[] = $_GET['date'];
    $types .= "s";
}

$query = "SELECT b.id AS booking_id, b.booking_date, f.flight_number, f.source, f.destination, f.date, f.time
          FROM bookings b
          JOIN flights f ON b.flight_id = f.id
          WHERE 1 $filter_sql
          ORDER BY b.id DESC";

$stmt = $conn->prepare($query);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #f0f0f0, #dbe9f4);
            padding: 40px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .home-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .home-btn:hover {
            background: #5a6268;
        }
        form.filter-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        form.filter-form input, form.filter-form button {
            padding: 10px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        form.filter-form button {
            background: #007BFF;
            color: white;
            border: none;
        }
        form.filter-form button:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .actions a {
            display: inline-block;
            padding: 6px 10px;
            font-size: 13px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            color: white;
            margin-top: 5px;
            margin-right: 5px;
        }
        .cancel {
            background: #dc3545;
        }
        .cancel:hover {
            background: #c82333;
        }
        .print {
            background: #28a745;
        }
        .print:hover {
            background: #218838;
        }
    </style>
    <script>
        function confirmCancel(id) {
            if (confirm("Are you sure you want to cancel booking #" + id + "?")) {
                window.location.href = "view-bookings.php?cancel=" + id;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="dashboard.php"><button class="home-btn">🏠 Home</button></a>
        <h2>All Flight Bookings</h2>

        <form method="get" class="filter-form">
            <input type="text" name="source" placeholder="Source" value="<?= htmlspecialchars($_GET['source'] ?? '') ?>">
            <input type="text" name="destination" placeholder="Destination" value="<?= htmlspecialchars($_GET['destination'] ?? '') ?>">
            <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
            <button type="submit">Filter</button>
            <a href="view-bookings.php" style="text-decoration: none;"><button type="button">Reset</button></a>
        </form>

        <table>
            <tr>
                <th>Booking ID</th>
                <th>Flight</th>
                <th>Route</th>
                <th>Date & Time</th>
                <th>Booking Date</th>
                <th>Passengers</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $booking_id = $row['booking_id'];
                    $stmt_p = $conn->prepare("SELECT * FROM passengers WHERE booking_id = ?");
                    $stmt_p->bind_param("i", $booking_id);
                    $stmt_p->execute();
                    $passengers = $stmt_p->get_result();
                ?>
                <tr>
                    <td><?= $booking_id ?></td>
                    <td><?= $row['flight_number'] ?></td>
                    <td><?= $row['source'] ?> ➝ <?= $row['destination'] ?></td>
                    <td><?= $row['date'] ?> <?= $row['time'] ?></td>
                    <td><?= $row['booking_date'] ?></td>
                    <td>
                        <ul style="margin: 0; padding: 0; list-style: none;">
                            <?php while ($p = $passengers->fetch_assoc()): ?>
                                <li style="margin-bottom: 10px;">
                                    <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                                    DOB: <?= $p['dob'] ?><br>
                                    Email: <?= $p['email'] ?><br>
                                    Phone: <?= $p['phone'] ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </td>
                    <td class="actions">
                        <a href="javascript:void(0);" class="cancel" onclick="confirmCancel(<?= $booking_id ?>)">Cancel</a>
                        <a href="print-ticket.php?id=<?= $booking_id ?>" class="print" target="_blank">Print</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

