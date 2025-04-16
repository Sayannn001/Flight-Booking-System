<?php
session_start();
include 'includes/db.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($_POST["password"], $result["password"])) {
        $_SESSION["admin"] = $result["id"];
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login - Flight Booking System</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-image: url('https://images.wallpaperscraft.com/image/single/plane_sky_clouds_70175_1920x1080.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center center;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      backdrop-filter: blur(10px);
      background: rgba(0, 0, 0, 0.5);
      padding: 50px 40px;
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
      max-width: 400px;
      width: 100%;
      color: #fff;
      text-align: center;
    }

    h2 {
      margin-bottom: 10px;
      font-size: 28px;
    }

    .intro {
      font-size: 14px;
      margin-bottom: 25px;
      line-height: 1.6;
      color: #ddd;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input {
      padding: 12px;
      font-size: 16px;
      border-radius: 5px;
      border: none;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }

    input::placeholder {
      color: #eee;
    }

    button {
      padding: 12px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      background: #007BFF;
      color: white;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: #0056b3;
    }

    .message {
      background: rgba(255, 0, 0, 0.2);
      color: #ffecec;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    .footer-note {
      margin-top: 30px;
      font-size: 13px;
      color: #ccc;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>✈️ Admin Login</h2>
    <div class="intro">
      Welcome to the Flight Booking System Admin Panel.<br>
      Please log in using your administrator credentials to manage flights, bookings, and passengers.
    </div>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="username" placeholder="Admin Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
    </form>

    <div class="footer-note">
      Access restricted to authorized airline staff only.
    </div>
  </div>
</body>
</html>
