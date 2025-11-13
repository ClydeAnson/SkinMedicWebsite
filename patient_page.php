<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
  header('Location: index.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT firstName, lastName, profile_picture FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user = $query->get_result()->fetch_assoc();

$clientName = htmlspecialchars($user['firstName'] . ' ' . $user['lastName']);
$profilePic = !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SkinMedic | Patient Dashboard</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  display: flex;
  background: #ffffff;
  color: #2f2a27;
}
.sidebar {
  width: 250px;
  min-height: 100vh;
  background: #f5f5f5;
  padding: 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-right: 1px solid #ddd;
  position: fixed;
  top: 0;
  left: 0;
}
.sidebar-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  margin-top: 30px;
}
.profile-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 6px 8px;
  margin-bottom: 12px;
}
.profile-wrap img {
  width: 55px;
  height: 55px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #80a833;
}
.profile-wrap .client-name {
  font-weight: 600;
  color: #3a3a3a;
  font-size: 15px;
}
.menu {
  width: 100%;
  margin-top: 10px;
  text-align: left;
  padding-left: 20px;
}
.menu a {
  display: block;
  text-decoration: none;
  color: #334155;
  padding: 10px 12px;
  border-radius: 6px;
  margin-bottom: 8px;
  transition: 0.18s ease;
  font-weight: 500;
  cursor: pointer;
}
.menu a:hover,
.menu a.active {
  background: #80a833;
  color: #fff;
}
.logo-wrap {
  width: 100%;
  text-align: center;
  margin-top: 25px;
  margin-bottom: 20px;
}
.logo-wrap img {
  width: 110px;
  height: auto;
}
.logout-btn {
  display: inline-block;
  padding: 8px 20px;
  border-radius: 25px;
  background: #80a833;
  color: #fff;
  font-weight: 500;
  text-decoration: none;
  font-size: 14px;
  transition: 0.2s;
  position: fixed;
  bottom: 30px;
  left: 85px;
}
.logout-btn:hover {
  background: #6c8f29;
}
.main-content {
  margin-left: 250px;
  flex: 1;
  padding: 24px 48px;
  background: #fff;
  min-height: 100vh;
  overflow-y: auto;
}
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
</style>
</head>
<body>

<!-- === SIDEBAR === -->
<aside class="sidebar">
  <div class="sidebar-inner">
    <div class="profile-wrap">
      <img src="<?= $profilePic ?>" alt="Profile Picture">
      <div class="client-name"><?= $clientName ?></div>
    </div>

    <div class="menu">
      <a href="#" data-section="home" id="homeBtn" class="active">Home</a>
      <a href="#" data-section="ar">AR Skin Analysis</a>
      <a href="#" data-section="product">Products</a>
      <a href="#" data-section="services">Services</a>
      <a href="#" data-section="sessions">Scheduled Sessions</a>
      <a href="#" data-section="mybookings">My Bookings</a>
      <a href="#" data-section="review">Review</a>
      <a href="#" data-section="profile">Profile</a>
    </div>

    <div class="logo-wrap">
      <img src="skintransparent.png" alt="Logo">
    </div>
  </div>

  <a href="logout.php" class="logout-btn">Log Out</a>
</aside>

<!-- === MAIN CONTENT === -->
<main class="main-content" id="mainContent">
  <div class="hero">
    <div class="pre">Welcome</div>
    <div class="title">Skin Medic</div>
    <div class="subtitle">Your journey to radiant, healthy skin begins here</div>
    <div style="margin-top: 20px;">
      <button onclick="loadSection('services')" style="background:#80a833;color:white;border:none;padding:10px 20px;border-radius:25px;margin-right:10px;font-weight:500;cursor:pointer;">Book a Session</button>
      <button onclick="loadSection('ar')" style="background:#80a833;color:white;border:none;padding:10px 20px;border-radius:25px;font-weight:500;cursor:pointer;">AR Skin Analysis</button>
    </div>
  </div>
</main>

<!-- === SCRIPT === -->
<script>
async function loadSection(section) {
  const map = {
    home: 'home.php',
    ar: 'ar_.php',
    product: 'product.php',
    services: 'patient_services.php',
    sessions: 'scheduled_sessions.php',
    mybookings: 'bookings_history.php',
    review: 'review.php',
    profile: 'profile.php'
  };

  const fileToLoad = map[section];
  if (!fileToLoad) return;

  try {
    const res = await fetch(fileToLoad + '?v=' + Date.now());
    if (!res.ok) throw new Error('Page not found');
    const html = await res.text();

    document.getElementById('mainContent').innerHTML = html;

    // execute inline scripts in the loaded page
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    doc.querySelectorAll("script").forEach(script => {
      const newScript = document.createElement("script");
      if (script.src) newScript.src = script.src;
      else newScript.textContent = script.textContent;
      document.body.appendChild(newScript);
    });

  } catch (err) {
    document.getElementById('mainContent').innerHTML = "<p style='color:red;'>❌ Failed to load page.</p>";
    console.error(err);
  }
}

// Sidebar navigation
document.querySelectorAll('.menu a').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const section = e.currentTarget.dataset.section;

    document.querySelectorAll('.menu a').forEach(a => a.classList.remove('active'));
    e.currentTarget.classList.add('active');

    if (section === 'home') {
      document.getElementById('mainContent').innerHTML = `
        <div class="hero">
          <div class="pre">Welcome</div>
          <div class="title">Skin Medic</div>
          <div class="subtitle">Your journey to radiant, healthy skin begins here</div>
          <div style="margin-top: 20px;">
            <button onclick="loadSection('services')" style="background:#80a833;color:white;border:none;padding:10px 20px;border-radius:25px;margin-right:10px;font-weight:500;cursor:pointer;">Book a Session</button>
            <button onclick="loadSection('ar')" style="background:#80a833;color:white;border:none;padding:10px 20px;border-radius:25px;font-weight:500;cursor:pointer;">AR Skin Analysis</button>
          </div>
        </div>`;
    } else {
      loadSection(section);
    }
  });
});

// ✅ Keep your original global booking button logic
document.addEventListener("click", e => {
  if (e.target && e.target.classList.contains("book-btn")) {
    e.preventDefault();
    const id = e.target.dataset.service;
    if (!id) return;
    fetch("book_service.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "service_id=" + encodeURIComponent(id)
    })
    .then(r => r.text())
    .then(d => alert(d.includes("success") ? "✅ Appointment booked!" : "❌ Booking failed."))
    .catch(() => alert("Error booking service."));
  }
});
</script>

</body>
</html>
