<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not found in session.");
}

$user_id = intval($_SESSION['user_id']);

// Get service ID
$service_id = $_GET['service_id'] ?? $_GET['id'] ?? $_GET['treatment_id'] ?? null;
if (!$service_id) {
    die("Error: No service ID provided.");
}

// Fetch user info
$userQuery = $conn->query("SELECT firstName, lastName, email FROM users WHERE user_id = $user_id");
$user = $userQuery->fetch_assoc();

// Fetch service info
$serviceQuery = $conn->query("SELECT * FROM services WHERE service_id = $service_id");
$service = $serviceQuery->fetch_assoc();

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    $stmt = $conn->prepare("
        INSERT INTO appointments (service_id, user_id, appointment_date, appointment_time)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $service_id, $user_id, $date, $time);

    if ($stmt->execute()) {
        echo "
        <script>
            alert('🎉 Appointment booked successfully!');
            window.location.href = 'patient_services.php';
        </script>
        ";
    } else {
        echo "<script>alert('Error booking appointment.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        input, select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            background: #80a833;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
        }
        button:hover {
            background: #6f8e2d;
        }
        .service-name {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .back-btn {
            text-align: center;
            margin-top: 10px;
            display: block;
            color: #80a833;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Book Your Appointment</h2>

    <?php if ($service): ?>
        <p class="service-name">
            <?= htmlspecialchars($service['name']) ?> — ₱<?= htmlspecialchars($service['price']) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" value="<?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>" readonly>

        <label>Email</label>
        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

        <label>Appointment Date</label>
        <input type="date" name="appointment_date" id="appointment_date" required>

        <label>Available Time</label>
        <select name="appointment_time" id="appointment_time" required>
            <option value="">Select date first</option>
        </select>

        <button type="submit">Confirm Booking</button>
    </form>

    <a href="patient_services.php" class="back-btn">← Back to Services</a>
</div>

<script>
document.getElementById('appointment_date').addEventListener('change', function () {
    const date = this.value;
    const serviceId = <?= $service_id ?>;
    const timeSelect = document.getElementById('appointment_time');

    timeSelect.innerHTML = '<option>Loading...</option>';

    fetch(`get_available_times.php?date=${date}&service_id=${serviceId}`)
        .then(response => response.json())
        .then(times => {
            timeSelect.innerHTML = '<option value="">Select available time</option>';

            if (times.length === 0) {
                timeSelect.innerHTML = '<option>No available time</option>';
                return;
            }

            times.forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
            });
        });
});
</script>

</body>
</html>
