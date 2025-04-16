<?php
include 'includes/auth.php';
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_flight'])) {
    $stmt = $conn->prepare("INSERT INTO flights (flight_number, source, destination, date, time, total_seats)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $_POST["flight_number"], $_POST["source"], $_POST["destination"],
                      $_POST["date"], $_POST["time"], $_POST["total_seats"]);
    $stmt->execute();
    $success = "✅ Flight added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Flight</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            padding: 40px;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
        }
        .home-btn {
            padding: 10px 16px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        .form-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }
        .form-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
        }
        .form-card input, .form-card button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .form-card button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            border: none;
        }
        .success {
            background: #d4edda;
            padding: 10px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
            border-radius: 6px;
            color: #155724;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>✈️ Add New Flight</h2>
        <a class="home-btn" href="dashboard.php">🏠 Home</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="form-card">
        <h3>Flight Details</h3>
        <form method="post">
            <input type="text" name="flight_number" placeholder="Flight Number" required>
            <input type="text" name="source" placeholder="Source" required>
            <input type="text" name="destination" placeholder="Destination" required>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <input type="number" name="total_seats" placeholder="Total Seats" required>
            <button type="submit" name="add_flight">➕ Add Flight</button>
        </form>
    </div>
</div>
</body>
</html>
