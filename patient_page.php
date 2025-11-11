<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Fetch client name for sidebar
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT firstName, lastName FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user = $query->get_result()->fetch_assoc();
$clientName = htmlspecialchars($user['firstName'] . ' ' . $user['lastName']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SkinMedic | Patient Dashboard</title>
<style>
/* === Base + Reset === */
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  display: flex;
  background: #ffffff;
  color: #2f2a27;
}

/* === SIDEBAR === */
.sidebar {
  width: 250px;
  min-height: 100vh;
  background: #e8e9eb;
  padding: 28px 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-right: 1px solid #e5e7eb;
  position: fixed;
  top: 0;
  left: 0;
}

.logo-wrap {
  text-align: center;
  margin-bottom: 10px;
}

.logo-wrap img {
  width: 180px;
  height: auto;
  display: block;
  margin: 0 auto;
}

.client-name {
  font-weight: 600;
  color: #3a3a3a;
  margin-top: 10px;
  font-size: 16px;
  text-align: center;
}

.menu {
  width: 100%;
  margin-top: 20px;
  flex-grow: 1;
}

.menu a {
  display: block;
  text-decoration: none;
  color: #334155;
  padding: 10px 15px;
  border-radius: 6px;
  margin-bottom: 10px;
  transition: 0.3s ease;
  font-weight: 500;
}

.menu a:hover,
.menu a.active {
  background: #80a833;
  color: #fff;
}

/* === Logout Button === */
.logout-btn {
  display: inline-block;
  padding: 8px 20px;
  border-radius: 25px;
  background: #80a833;
  color: #fff;
  font-weight: 500;
  text-decoration: none;
  font-size: 14px;
  transition: 0.3s;
  margin-top: auto;
  margin-bottom: 10px;
}
.logout-btn:hover {
  background: #6c8f29;
}

/* === MAIN CONTENT === */
.main-content {
  margin-left: 250px;
  flex: 1;
  padding: 24px 48px;
  background: #fff;
}

/* === Hero === */
.hero {
  text-align: center;
  padding: 56px 20px 20px;
}
.hero .pre {
  font-size: 34px;
  color: #80a833;
  margin-bottom: 6px;
  font-weight: 600;
}
.hero .title {
  font-family: 'Playfair Display', serif;
  font-size: 80px;
  margin: 6px 0;
  color: #5d595f;
  line-height: 1;
}
.hero .subtitle {
  color: #80a833;
  font-size: 20px;
  margin: 12px 0 24px;
  font-weight: 600;
}

/* === Floating AR Popup === */
.ar-popup {
  position: fixed;
  top: 52%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 600px;
  max-width: 90%;
  background: #fffbe9;
  border: 2px solid #e2d6a8;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  border-radius: 20px;
  padding: 30px;
  z-index: 100;
  text-align: center;
  animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translate(-50%, -60%); }
  to { opacity: 1; transform: translate(-50%, -50%); }
}
.ar-popup h2 {
  font-size: 28px;
  color: #4b423a;
  margin-bottom: 10px;
}
.ar-popup p {
  color: #5c544c;
  font-size: 15px;
  line-height: 1.6;
  margin-bottom: 20px;
}
.ar-popup button {
  background: #80a833;
  color: #fff;
  border: none;
  border-radius: 25px;
  padding: 10px 24px;
  font-size: 15px;
  cursor: pointer;
  transition: 0.3s;
}
.ar-popup button:hover {
  background: #6b8f29;
}

/* === Responsive === */
@media (max-width: 820px){
  .sidebar {
    position: relative;
    width: 100%;
    flex-direction: row;
    align-items: center;
    padding: 10px 20px;
  }
  .main-content { margin-left: 0; padding: 16px; }
  .menu { display: none; }
}
</style>
</head>
<body>

<!-- === Sidebar === -->
<aside class="sidebar">
  <div class="logo-wrap">
    <img src="skintransparent.png" alt="Logo">
    <div class="client-name"><?= $clientName ?></div>
  </div>

  <div class="menu">
    <a href="?section=home" class="active">Home</a>
    <a href="?section=ar">AR Skin Analysis</a>
    <a href="?section=products">Products</a>
    <a href="?section=services">Services</a>
    <a href="?section=sessions">Scheduled Sessions</a>
    <a href="?section=mybookings">My Bookings</a>
    <a href="?section=store">Store</a>
    <a href="?section=review">Review</a>
    <a href="?section=profile">Profile</a>
  </div>

  <a href="logout.php" class="logout-btn">Log Out</a>
</aside>

<!-- === Main Content === -->
<main class="main-content">
  <div class="hero">
    <div class="pre">Welcome</div>
    <div class="title">Skin Medic</div>
    <div class="subtitle">Your portal for treatments and bookings</div>
  </div>
</main>

<!-- === Floating AR Popup === -->
<div class="ar-popup" id="arPopup">
  <h2>✨ AR Skin Analysis</h2>
  <p>Experience real-time augmented skin analysis to detect acne, blemishes, and more with precision. Start scanning to get instant insights about your skin.</p>
  <button onclick="closePopup()">Close</button>
</div>

<script>
function closePopup() {
  document.getElementById('arPopup').style.display = 'none';
}
</script>

</body>
</html>
