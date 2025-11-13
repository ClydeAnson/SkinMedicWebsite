<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
  header('Location: index.php');
  exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$userQuery = $conn->prepare("SELECT firstName, lastName, profile_picture FROM users WHERE user_id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$currentUser = $userQuery->get_result()->fetch_assoc();
$profilePic = !empty($currentUser['profile_picture']) ? htmlspecialchars($currentUser['profile_picture']) : 'default-profile.png';

// Handle AJAX submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
  $review = trim($_POST['review_text']);
  $rating = intval($_POST['rating']);
  if ($rating < 1) $rating = 1;
  if ($rating > 5) $rating = 5;

  $stmt = $conn->prepare("INSERT INTO reviews (user_id, review_text, rating, created_at) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("isi", $user_id, $review, $rating);
  $stmt->execute();
  exit('success');
}

// Fetch all reviews
$query = "
    SELECT r.review_text, r.rating, r.created_at, u.firstName, u.lastName, u.profile_picture
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    ORDER BY r.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Reviews | SkinMedic</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #fffdf9;
  margin: 0;
  padding: 30px;
  color: #2f2a27;
}
.container {
  max-width: 800px;
  margin: 0 auto;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 14px rgba(0,0,0,0.08);
  padding: 30px;
}
h2 {
  text-align: center;
  color: #2f2a27;
  margin-bottom: 25px;
}
form {
  background: #f5f5f5;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 30px;
}
form textarea {
  width: 100%;
  height: 100px;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 10px;
  resize: none;
  font-size: 14px;
  font-family: 'Poppins';
}
.rating {
  display: flex;
  justify-content: flex-start;
  gap: 4px;
  margin: 8px 0 14px;
}
.rating input {
  display: none;
}
.rating label {
  font-size: 24px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.2s;
}
.rating input:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
  color: #80a833;
}
button {
  background: #80a833;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
button:hover {
  background: #6c8f29;
}

/* Review feed */
.review-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  background: #fff;
  border-bottom: 1px solid #eee;
  padding: 16px 0;
}
.review-card img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #80a833;
}
.review-content {
  flex: 1;
}
.review-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.review-header .name {
  font-weight: 600;
  font-size: 15px;
  color: #2f2a27;
}
.review-header .date {
  font-size: 12px;
  color: #888;
}
.stars {
  color: #80a833;
  font-size: 16px;
  margin-top: 2px;
}
.review-text {
  margin-top: 6px;
  font-size: 15px;
  line-height: 1.5;
  color: #3f3a37;
}
.no-reviews {
  text-align: center;
  color: #888;
  margin-top: 20px;
  font-style: italic;
}
</style>
</head>
<body>
<div class="container">
  <h2>📝 Patient Reviews</h2>

  <form id="reviewForm">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
      <img src="<?= $profilePic ?>" alt="Profile" width="45" height="45" style="border-radius:50%; object-fit:cover; border:2px solid #80a833;">
      <strong><?= htmlspecialchars($currentUser['firstName'] . ' ' . $currentUser['lastName']) ?></strong>
    </div>

    <textarea name="review_text" placeholder="Write your review..." required></textarea>
    
    <div class="rating" style="flex-direction: row-reverse;">
      <input type="radio" id="star5" name="rating" value="5"><label for="star5">★</label>
      <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
      <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
      <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
      <input type="radio" id="star1" name="rating" value="1" checked><label for="star1">★</label>
    </div>

    <button type="submit">Post Review</button>
  </form>

  <h3 style="margin-bottom: 15px;">Recent Reviews</h3>

  <div id="reviewList">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="review-card">
          <img src="<?= !empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'default-profile.png' ?>" alt="Profile">
          <div class="review-content">
            <div class="review-header">
              <div class="name"><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></div>
              <div class="date"><?= date('F j, Y g:i A', strtotime($row['created_at'])) ?></div>
            </div>
            <div class="stars"><?= str_repeat('★', $row['rating']) ?></div>
            <div class="review-text"><?= nl2br(htmlspecialchars($row['review_text'])) ?></div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-reviews">No reviews yet. Be the first to leave one!</p>
    <?php endif; ?>
  </div>
</div>

<script>
document.getElementById('reviewForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('action', 'add_review');

  const res = await fetch('review.php', { method: 'POST', body: formData });
  const text = await res.text();

  if (text.trim() === 'success') {
    // Reload review section without full reload
    const updated = await fetch('review.php?v=' + Date.now());
    const html = await updated.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    document.getElementById('reviewList').innerHTML = doc.getElementById('reviewList').innerHTML;
    this.reset();
    alert('✅ Review posted successfully!');
  } else {
    alert('❌ Failed to post review.');
  }
});
</script>
</body>
</html>
