<?php
include 'includes/auth.php';
include 'includes/db.php';

$flights = [];

if (isset($_POST["search"])) {
    $stmt = $conn->prepare("SELECT * FROM flights WHERE source = ? AND destination = ? AND date = ?");
    $stmt->bind_param("sss", $_POST["source"], $_POST["destination"], $_POST["date"]);
    $stmt->execute();
    $flights = $stmt->get_result();
}

if (isset($_POST["book"])) {
    $flight_id = $_POST["flight_id"];
    $booking_date = date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO bookings (flight_id, booking_date) VALUES (?, ?)");
    $stmt->bind_param("is", $flight_id, $booking_date);
    $stmt->execute();
    $booking_id = $stmt->insert_id;

    $passenger_count = $_POST["adult_count"] + $_POST["child_count"];

    for ($i = 0; $i < $passenger_count; $i++) {
        $name = $_POST["name"][$i];
        $dob = $_POST["dob"][$i];
        $email = $_POST["email"][$i];
        $phone = $_POST["phone"][$i];
        $gender = $_POST["gender"][$i];

        $stmt_p = $conn->prepare("INSERT INTO passengers (booking_id, name, dob, email, phone, gender) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_p->bind_param("isssss", $booking_id, $name, $dob, $email, $phone, $gender);
        $stmt_p->execute();
    }

    echo "<div style='text-align:center; font-weight:bold; margin-top:20px;'>✅ Booking successful! <a href='view-bookings.php'>View All Bookings</a></div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Flight</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #f0f0f0, #dbe9f4);
            padding: 40px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2, h3, h4 {
            text-align: center;
            color: #333;
        }
        input, select, button {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
        }
        button:hover {
            background: #0056b3;
        }
        .home-btn {
            background: #6c757d;
            margin-bottom: 20px;
        }
        .passenger-card {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .summary-card {
            background: #e8f4ff;
            border: 1px solid #007BFF;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
        }
    </style>

    <script>
        function showPassengerFields() {
            const adultCount = parseInt(document.getElementById('adult_count').value || 0);
            const childCount = parseInt(document.getElementById('child_count').value || 0);
            const total = adultCount + childCount;

            const container = document.getElementById('passenger-fields');
            container.innerHTML = "";

            for (let i = 0; i < total; i++) {
                const div = document.createElement("div");
                div.className = "passenger-card";
                div.innerHTML = `
                    <h4>Passenger ${i + 1}</h4>
                    <input name="name[]" placeholder="Full Name" required>
                    <input type="date" name="dob[]" placeholder="Date of Birth" required>
                    <input type="email" name="email[]" placeholder="Email" required>
                    <input name="phone[]" placeholder="Phone Number" required>
                    <select name="gender[]" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                `;
                container.appendChild(div);
            }

            document.getElementById('passenger-details').style.display = "block";
        }

        function showSummary(event) {
            event.preventDefault();

            const names = document.getElementsByName("name[]");
            const dobs = document.getElementsByName("dob[]");
            const emails = document.getElementsByName("email[]");
            const phones = document.getElementsByName("phone[]");
            const genders = document.getElementsByName("gender[]");

            const summaryContainer = document.getElementById("summary");
            summaryContainer.innerHTML = "<h3>Passenger Summary</h3>";

            for (let i = 0; i < names.length; i++) {
                const div = document.createElement("div");
                div.className = "summary-card";
                div.innerHTML = `
                    <strong>Name:</strong> ${names[i].value}<br>
                    <strong>Date of Birth:</strong> ${dobs[i].value}<br>
                    <strong>Email:</strong> ${emails[i].value}<br>
                    <strong>Phone:</strong> ${phones[i].value}<br>
                    <strong>Gender:</strong> ${genders[i].value}
                `;
                summaryContainer.appendChild(div);
            }

            document.getElementById("summary-section").style.display = "block";
        }
    </script>
</head>
<body>
    <div class="container">
        <a href="dashboard.php"><button class="home-btn">🏠 Home</button></a>
        <h2>Search Flights</h2>
        <form method="post">
            <input name="source" placeholder="Source" required>
            <input name="destination" placeholder="Destination" required>
            <input type="date" name="date" required>
            <button name="search" type="submit">Search</button>
        </form>

        <?php if (!empty($flights) && $flights->num_rows > 0): ?>
            <form method="post" id="booking-form">
                <h3>Select Flight</h3>
                <select name="flight_id" required>
                    <?php while ($f = $flights->fetch_assoc()): ?>
                        <option value="<?= $f['id'] ?>">
                            <?= $f['flight_number'] ?> | <?= $f['source'] ?> ➡ <?= $f['destination'] ?> | <?= $f['date'] ?> <?= $f['time'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <h3>Number of Passengers</h3>
                <input type="number" id="adult_count" name="adult_count" placeholder="No. of Adults" required>
                <input type="number" id="child_count" name="child_count" placeholder="No. of Children" required>
                <button type="button" onclick="showPassengerFields()">Add Passenger Details</button>

                <div id="passenger-details" style="display: none;">
                    <h3>Passenger Details</h3>
                    <div id="passenger-fields"></div>
                    <button type="button" onclick="showSummary(event)">Show Summary</button>
                    <div id="summary-section" style="display:none; margin-top:20px;">
                        <div id="summary"></div>
                        <button type="submit" name="book">Confirm Booking</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
