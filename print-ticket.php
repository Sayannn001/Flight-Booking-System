<?php 
include 'includes/db.php';

$id = $_GET["id"];

// Get flight info
$stmt = $conn->prepare("SELECT f.id as flight_id, f.flight_number, f.source, f.destination, f.date, f.time, f.seats
                        FROM bookings b JOIN flights f ON b.flight_id = f.id
                        WHERE b.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();

$flight_id = $flight['flight_id'];
$totalSeats = $flight['seats'];

// Generate a consistent gate number for this flight
srand($flight_id); // Seed for repeatable gate per flight
$gateNumber = rand(1, 12);
$gateSuffixes = ['A', 'B', 'C'];
$gate = $gateNumber . $gateSuffixes[array_rand($gateSuffixes)];

// Generate full seat map
$seatLetters = ['A', 'B', 'C', 'D', 'E', 'F'];
$seatList = [];
for ($row = 1; $row <= ceil($totalSeats / 6); $row++) {
    foreach ($seatLetters as $letter) {
        $seatList[] = $row . $letter;
        if (count($seatList) >= $totalSeats) break;
    }
}

// Get already assigned seats for this flight
$assignedSeatQuery = "
    SELECT p.seat 
    FROM passengers p 
    JOIN bookings b ON p.booking_id = b.id 
    WHERE b.flight_id = ? AND p.seat IS NOT NULL
";
$stmt = $conn->prepare($assignedSeatQuery);
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();

$alreadyAssignedSeats = [];
while ($row = $result->fetch_assoc()) {
    $alreadyAssignedSeats[] = $row['seat'];
}

// Get current passengers
$stmt = $conn->prepare("SELECT * FROM passengers WHERE booking_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$passengers = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 30px;
    }
    .ticket {
      max-width: 600px;
      margin: 20px auto;
      background: #fff;
      padding: 20px;
      border: 2px dashed #333;
      border-radius: 10px;
    }
    .ticket h2 {
      text-align: center;
      margin-top: 0;
    }
    .ticket-section {
      display: flex;
      justify-content: space-between;
      margin: 10px 0;
    }
    .label {
      font-weight: bold;
    }
    .barcode {
      margin-top: 20px;
      text-align: center;
    }
    .barcode img {
      width: 120px;
    }
  </style>
</head>
<body>

<?php while ($p = $passengers->fetch_assoc()): 
    // Assign a seat only if not already assigned
    if (empty($p['seat'])) {
        do {
            $seat = $seatList[array_rand($seatList)];
        } while (in_array($seat, $alreadyAssignedSeats));

        $alreadyAssignedSeats[] = $seat;

        // Update the passenger's seat in DB
        $update = $conn->prepare("UPDATE passengers SET seat = ? WHERE id = ?");
        $update->bind_param("si", $seat, $p['id']);
        $update->execute();
    } else {
        $seat = $p['seat']; // already assigned
    }
?>
  <div class="ticket">
    <h2>✈️ Flight Ticket</h2>
    <div class="ticket-section">
      <div><span class="label">Flight:</span> <?= $flight['flight_number'] ?></div>
      <div><span class="label">Date:</span> <?= $flight['date'] ?> <?= $flight['time'] ?></div>
    </div>
    <div class="ticket-section">
      <div><span class="label">From:</span> <?= $flight['source'] ?></div>
      <div><span class="label">To:</span> <?= $flight['destination'] ?></div>
    </div>
    <hr>
    <div class="ticket-section">
      <div><span class="label">Passenger:</span> <?= $p['name'] ?></div>
      <div><span class="label">DOB:</span> <?= $p['dob'] ?></div>
    </div>
    <div class="ticket-section">
      <div><span class="label">Email:</span> <?= $p['email'] ?></div>
      <div><span class="label">Phone:</span> <?= $p['phone'] ?></div>
    </div>
    <hr>
    <div class="ticket-section">
      <div><span class="label">Gate:</span> <?= $gate ?></div>
      <div><span class="label">Seat:</span> <?= $seat ?></div>
      <div><span class="label">Booking ID:</span> <?= $p['booking_id'] ?></div>
    </div>
    <div class="barcode">
      <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= $p['id'] ?>&size=100x100" alt="QR Code">
    </div>
  </div>
<?php endwhile; ?>

</body>
</html>
