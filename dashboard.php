<?php
include 'includes/auth.php';
include 'includes/db.php';

// Get admin ID from session
$adminId = $_SESSION['admin'] ?? null;
$adminName = "Admin";

if ($adminId) {
  $stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
  $stmt->bind_param("i", $adminId);
  $stmt->execute();
  $stmt->bind_result($fetchedUsername);
  if ($stmt->fetch()) {
      $adminName = $fetchedUsername;
  }
  $stmt->close();
  
}

// Fetch stats
$totalFlights = $conn->query("SELECT COUNT(*) FROM flights")->fetch_row()[0];
$totalBookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetch_row()[0];
$upcomingFlights = $conn->query("SELECT COUNT(*) FROM flights WHERE date >= CURDATE()")->fetch_row()[0];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f2f6fa;
      padding: 40px;
      margin: 0;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .welcome {
      text-align: center;
      font-size: 18px;
      margin-bottom: 30px;
    }
    .stats {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
    }
    .card {
      flex: 1;
      background: #007BFF;
      color: #fff;
      padding: 25px;
      margin: 0 10px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .card h3 {
      margin: 0;
      font-size: 36px;
    }
    .card p {
      margin-top: 5px;
      font-size: 18px;
    }
    .menu {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
      margin-top: 20px;
    }
    .menu a {
      text-decoration: none;
      padding: 14px 20px;
      background: #343a40;
      color: #fff;
      border-radius: 6px;
      text-align: center;
      transition: background 0.3s ease;
      flex: 1 1 200px;
    }
    .menu a:hover {
      background: #212529;
    }
    @media (max-width: 768px) {
      .stats {
        flex-direction: column;
        gap: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>✈️ Admin Dashboard</h2>
    <div class="welcome">Welcome, <strong><?= htmlspecialchars($adminName) ?></strong>!</div>

    <div class="stats">
      <div class="card">
        <h3><?= $totalFlights ?></h3>
        <p>Total Flights</p>
      </div>
      <div class="card" style="background:#28a745;">
        <h3><?= $totalBookings ?></h3>
        <p>Total Bookings</p>
      </div>
      <div class="card" style="background:#ffc107; color: #333;">
        <h3><?= $upcomingFlights ?></h3>
        <p>Upcoming Flights</p>
      </div>
    </div>

    <div class="menu">
      <a href="add-flight.php">➕ Add Flight</a>
      <a href="view-flights.php">✈️ View Flights</a>
      <a href="book-flight.php">🧾 Book Flight</a>
      <a href="view-bookings.php">📋 View Bookings</a>
      <a href="cancel-booking.php">❌ Cancel Booking</a>
      <a href="logout.php">🚪 Logout</a>
    </div>
  </div>
</body>
</html>
