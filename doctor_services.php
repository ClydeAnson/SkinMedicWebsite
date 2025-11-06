<?php
include 'config.php';

if (isset($_POST['toggle_status'])) {
    $service_id = $_POST['service_id'];
    $new_status = $_POST['new_status'];
    $sql = "UPDATE services SET status='$new_status' WHERE service_id=$service_id";
    mysqli_query($conn, $sql);
}

$result = mysqli_query($conn, "SELECT * FROM services");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Service Management - SkinMedic</title>
    <link rel="stylesheet" href="users_style.css">
    <style>
        .main {
            padding: 20px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .add-service-btn {
            background-color: #008080;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.2s;
        }

        .add-service-btn:hover {
            background-color: #006666;
        }

        .service-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .service-card {
            border-radius: 15px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
        }

        .service-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .service-card h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .service-card p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }

        .service-card .status {
            font-weight: bold;
            color: #008080;
        }

        .service-card .status.off {
            color: red;
        }
    </style>
</head>
<body style="background: #f8f8f8;">

<div class="main">
    <div class="topbar">
        <h2>Doctor Service Management</h2>
        <button class="add-service-btn" onclick="window.location.href='add_service.php'">+ Add New Service</button>
    </div>

    <div class="service-list">
        <?php
        include 'config.php';
        $query = "SELECT * FROM services";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $statusClass = $row['status'] == 'available' ? 'on' : 'off';
                echo "
                <div class='service-card'>
                    <img src='image/{$row['image']}' alt='{$row['name']}'>
                    <h3>{$row['name']}</h3>
                    <p>{$row['description']}</p>
                    <p class='status {$statusClass}'>Status: {$row['status']}</p>
                </div>
                ";
            }
        } else {
            echo "<p style='text-align:center; color:#666;'>No services added yet.</p>";
        }
        ?>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>