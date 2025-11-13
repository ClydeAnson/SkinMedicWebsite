<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Fetch all appointments for this user
$query = $conn->prepare("
    SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
           s.name AS service_name, s.price
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>
<div class="main">
  <div class="bookings-container">
    <h2>My Booking History</h2>

    <?php if ($result->num_rows > 0): ?>
      <table class="bookings-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($row = $result->fetch_assoc()):
              $status = htmlspecialchars($row['status']);
              $color = $status === 'Completed' ? '#80a833' : 
                       ($status === 'Pending' ? '#facc15' : 
                       ($status === 'Approved' ? '#3b82f6' : '#ef4444'));
          ?>
            <tr>
              <td><?= $count++ ?></td>
              <td><?= htmlspecialchars($row['service_name']) ?></td>
              <td><?= htmlspecialchars($row['appointment_date']) ?></td>
              <td><?= htmlspecialchars(date("g:i A", strtotime($row['appointment_time']))) ?></td>
              <td>₱<?= htmlspecialchars(number_format($row['price'], 2)) ?></td>
              <td style="font-weight:600; color:<?= $color ?>;">
                <?= $status ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-bookings">You don’t have any booking history yet.</p>
    <?php endif; ?>
  </div>
</div>

<style>
.bookings-container {
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  max-width: 1000px;
  margin: 30px auto;
}

.bookings-container h2 {
  text-align: center;
  color: #333;
  margin-bottom: 25px;
  font-size: 28px;
  border-bottom: 2px solid #80a833;
  display: inline-block;
  padding-bottom: 8px;
}

.bookings-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 15px;
}

.bookings-table th, .bookings-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #ddd;
  text-align: center;
}

.bookings-table th {
  background: #80a833;
  color: #fff;
  font-weight: 600;
}

.bookings-table tr:nth-child(even) {
  background: #f9fafb;
}

.bookings-table tr:hover {
  background: #eef3e5;
}

.no-bookings {
  text-align: center;
  font-size: 18px;
  color: #666;
  margin-top: 30px;
}
</style>
