<?php
include 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $_POST["booking_id"]);
    if ($stmt->execute()) {
        $message = "✅ Booking cancelled successfully!";
    } else {
        $message = "❌ Error cancelling booking.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancel Booking</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #f0f0f0, #dbe9f4);
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        input, button {
            padding: 12px;
            font-size: 16px;
            width: 80%;
            max-width: 400px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #c82333;
        }

        .home-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .home-btn:hover {
            background: #5a6268;
        }

        .message {
            margin-top: 20px;
            font-weight: bold;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 6px;
            display: inline-block;
        }

        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .note {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php"><button class="home-btn">🏠 Home</button></a>
        <h2>Cancel a Booking</h2>

        <form method="post">
            <input name="booking_id" placeholder="Enter Booking ID" required>
            <br>
            <button type="submit">Cancel Booking</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, '❌') !== false ? 'error' : '' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="note">
            Please ensure the booking ID is correct before cancelling.<br>
            This action cannot be undone.
        </div>
    </div>
</body>
</html>
