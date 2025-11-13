<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    exit('<p style="color:red;">You must be logged in to view this page.</p>');
}

$user_id = intval($_SESSION['user_id']);

// ✅ Handle delete request (AJAX or direct)
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    echo "<script>alert('Appointment deleted successfully!');</script>";
}

// ✅ Fetch all appointments for this user
$query = "
    SELECT a.appointment_id, 
           s.name AS service_name, 
           a.appointment_date, 
           a.appointment_time, 
           a.status
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 style="text-align:center; color:#80a833;">📅 Your Scheduled Sessions</h2>

<table style="width:90%; margin:20px auto; border-collapse:collapse; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:10px; overflow:hidden;">
  <tr style="background:#80a833; color:white;">
    <th style="padding:15px;">Service</th>
    <th style="padding:15px;">Date</th>
    <th style="padding:15px;">Time</th>
    <th style="padding:15px;">Status</th>
    <th style="padding:15px;">Action</th>
  </tr>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr style="text-align:center; border-bottom:1px solid #eee;">
        <td style="padding:12px;"><?= htmlspecialchars($row['service_name']) ?></td>
        <td style="padding:12px;"><?= htmlspecialchars($row['appointment_date']) ?></td>
        <td style="padding:12px;"><?= htmlspecialchars($row['appointment_time']) ?></td>
        <td style="padding:12px;"><?= htmlspecialchars($row['status']) ?></td>
        <td style="padding:12px;">
          <button class="delete-btn" onclick="confirmDelete(<?= $row['appointment_id'] ?>)" style="background:#e74c3c; color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer;">Delete</button>
        </td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="5" style="padding:15px;">No scheduled sessions found.</td></tr>
  <?php endif; ?>
</table>

<script>
function confirmDelete(id) {
  if (confirm('Are you sure you want to delete this appointment?')) {
    fetch('scheduled_sessions.php?delete_id=' + id)
      .then(res => res.text())
      .then(html => document.getElementById('mainContent').innerHTML = html);
  }
}
</script>
