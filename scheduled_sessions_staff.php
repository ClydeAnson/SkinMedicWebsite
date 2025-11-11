<?php
session_start();
include 'config.php';

// Only staff/admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['staff','admin'])) {
    header('Location: index.php');
    exit();
}

// Handle AJAX actions
if (isset($_POST['action'], $_POST['id'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    // Approve / Move / Decline
    if (in_array($action, ['Approved','Pending','Declined'])) {
        $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
        $stmt->bind_param("si", $action, $id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
        exit;
    }

    // Remove
    if ($action === 'Remove') {
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id=?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => $success]);
        exit;
    }

    echo json_encode(['success'=>false,'error'=>'Invalid action']);
    exit;
}

// Include sidebar
include 'sidebar_staff.php';

// Fetch all appointments
$result = $conn->query("SELECT * FROM appointments ORDER BY appointment_datetime ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scheduled Sessions - Staff</title>
<link rel="stylesheet" href="users_style.css">
<style>
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ccc; }
th { background-color: #7aac21; color: #fff; }
tr:nth-child(even) { background-color: #f9f9f9; }
.action-btn { padding: 5px 10px; margin-right: 4px; border: none; border-radius: 5px; cursor: pointer; }
.approve { background-color: #4caf50; color: white; }
.move { background-color: #2196f3; color: white; }
.decline { background-color: #f44336; color: white; }
.remove { background-color: #555; color: white; }
</style>
</head>
<body>

<main class="content">
    <h1>Scheduled Sessions</h1>

    <table id="sessionsTable">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Service</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr data-id="<?= $row['id'] ?>">
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['service']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($row['appointment_datetime'])) ?></td>
                <td class="status"><?= $row['status'] ?></td>
                <td>
                    <button class="action-btn approve" onclick="updateStatus(<?= $row['id'] ?>,'Approved')">Approve</button>
                    <button class="action-btn move" onclick="updateStatus(<?= $row['id'] ?>,'Pending')">Move</button>
                    <button class="action-btn decline" onclick="updateStatus(<?= $row['id'] ?>,'Declined')">Decline</button>
                    <button class="action-btn remove" onclick="updateStatus(<?= $row['id'] ?>,'Remove')">Remove</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<script>
// AJAX for updating booking status or removing
function updateStatus(id, action) {
    if (!confirm(`Are you sure you want to "${action}" this appointment?`)) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', action);

    fetch('<?= basename(__FILE__) ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (action === 'Remove') {
                const row = document.querySelector(`tr[data-id='${id}']`);
                row.parentNode.removeChild(row);
            } else {
                document.querySelector(`tr[data-id='${id}'] .status`).textContent = action;
            }
        } else {
            alert(data.error || 'Action failed.');
        }
    });
}
</script>

</body>
</html>
