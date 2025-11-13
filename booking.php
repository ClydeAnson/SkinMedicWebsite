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

// ✅ Get service ID from URL (supports multiple possible parameter names)
$service_id = $_GET['service_id'] ?? $_GET['id'] ?? $_GET['treatment_id'] ?? null;
if (!$service_id) {
    die("Error: No service ID provided in URL.");
}

// ✅ Fetch user info
$userQuery = $conn->query("SELECT firstName, lastName, email, contact FROM users WHERE user_id = $user_id");
$user = $userQuery->fetch_assoc();

// ✅ Fetch service info
$service = null;
$result = $conn->query("SELECT * FROM services WHERE service_id = $service_id");
$service = $result->fetch_assoc();

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $contact = $_POST['contact'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO appointments (service_id, user_id, appointment_date, appointment_time, contact, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $service_id, $user_id, $date, $time, $contact, $notes);

    if ($stmt->execute()) {
        echo "
        <div id='popup-overlay'>
            <div id='popup-box'>
                <h3>🎉 Booking Confirmed!</h3>
                <p>Your appointment has been successfully booked.</p>
                <button id='ok-btn'>OK</button>
            </div>
        </div>

        <script>
            // Style for popup overlay
            const style = document.createElement('style');
            style.textContent = `
                #popup-overlay {
                    position: fixed;
                    top: 0; left: 0;
                    width: 100vw; height: 100vh;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                }
                #popup-box {
                    background: white;
                    padding: 30px 40px;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                    text-align: center;
                    font-family: 'Poppins', sans-serif;
                    animation: fadeIn 0.3s ease-in-out;
                }
                #popup-box h3 {
                    color: #80a833;
                    margin-bottom: 10px;
                }
                #popup-box p {
                    margin-bottom: 20px;
                    color: #444;
                }
                #ok-btn {
                    background: #80a833;
                    border: none;
                    color: white;
                    padding: 10px 25px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 15px;
                }
                #ok-btn:hover {
                    background: #6f8e2d;
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: scale(0.9); }
                    to { opacity: 1; transform: scale(1); }
                }
            `;
            document.head.appendChild(style);

            document.addEventListener('DOMContentLoaded', () => {
                const okBtn = document.getElementById('ok-btn');
                okBtn.addEventListener('click', () => {
                    window.location.href = 'patient_page.php?section=services';
                });
            });
        </script>
        ";
    } else {
        echo "<script>alert('Error booking appointment. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; padding: 20px; }
        .container {
            max-width: 500px; margin: 50px auto; background: #fff;
            padding: 25px; border-radius: 15px; box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        form { display: flex; flex-direction: column; gap: 12px; }
        input, textarea {
            padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px;
        }
        textarea { resize: none; height: 80px; }
        button {
            background: #80a833; color: #fff; border: none; border-radius: 8px;
            padding: 10px; cursor: pointer; font-size: 15px;
        }
        button:hover { background: #6f8e2d; }
        .back-btn {
            display: block; text-align: center; margin-top: 15px;
            color: #80a833; text-decoration: none; font-weight: 500;
        }
        .back-btn:hover { text-decoration: underline; }
        .service-name {
            text-align: center; color: #333; font-weight: bold; margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Your Appointment</h2>
        <?php if ($service): ?>
            <p class="service-name"><?= htmlspecialchars($service['name']) ?> — ₱<?= htmlspecialchars($service['price']) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" value="<?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>" readonly>

            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

            <label>Contact Number</label>
            <input type="text" name="contact" value="<?= htmlspecialchars($user['contact'] ?? '') ?>" required>

            <label>Appointment Date</label>
            <input type="date" name="appointment_date" required>

            <label>Appointment Time</label>
            <input type="time" name="appointment_time" required>

            <label>Additional Notes (optional)</label>
            <textarea name="notes" placeholder="Any preferences or special requests?"></textarea>

            <button type="submit">Confirm Booking</button>
        </form>

        <a href="patient_page.php?section=services" class="back-btn">← Back to Services</a>
    </div>
</body>
</html>
