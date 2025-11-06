<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

switch ($_SESSION['role']) {
    case 'doctor':
        include 'sidebar_doctor.php';
        break;
    case 'staff':
        include 'sidebar_staff.php';
        break;
    default:
        include 'sidebar_patient.php';
        break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkinMedic</title>
    <link rel="stylesheet" href="users_style.css">
</head>
<body style="background: #fff;">
    
    <!-- Main Content -->
    <main class="content">
        <header class="header">
            <h2>Dashboard</h2>
            <div class="date-box">
                <p>Today's Date</p>
                <strong id="dateBox"><?= date("Y-m-d"); ?></strong>
            </div>
        </header>

        <section class="welcome-box">
            <h3>Welcome!</h3>
            <h1>Dr. <?= $_SESSION['lastName']; ?></h1>
            <p>Thanks for joining with us. You can view your daily schedule and reach your patients easily.</p>
            <button class="primary-btn">View My Appointments</button>
        </section>

        <section class="status">
            <div class="card">
                <h2>1</h2>
                <p>New Booking</p>
            </div>
            <div class="card">
                <h2>0</h2>
                <p>Today Sessions</p>
            </div>
        </section>

        <section class="upcoming">
            <h3>Your Upcoming Sessions until Next Week</h3>
            <div class="empty-box">
                <p>We couldn’t find anything related to your upcoming sessions.</p>
            </div>
        </section>
    </main>

</div>

<script src="script.js"></script>
</body>
</html>