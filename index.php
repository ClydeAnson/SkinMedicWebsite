<?php
include 'config.php';
$query = "SELECT name, description, price, image FROM services LIMIT 6";
$result = $conn->query($query);
$year = date("Y");
$nav = [
  'Book Appointment','AR Skin Analysis','Treatment and services',
  'Shop','My profile','Reviews','Settings'
];

session_start();
if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'doctor':
            header('Location: doctor_page.php');
            exit();
        case 'staff':
            header('Location: staff_page.php');
            exit();
        case 'patient':
            header('Location: patient_page.php');
            exit();
    }
}
$showLoginPopup = isset($_GET['login']); // ?login in URL triggers the popup

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Skin Medic — Home</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="index.css">
</head>
<body>
  <aside class="sidebar">
    <div class="logo-wrap">
      <!-- use local asset if available -->
      <img src="skintransparent.png" alt="Skin Medic logo" onerror="this.src='https://i.ibb.co/1bZ1kRS/lotus.png'"/>
      <div class="brand">
      </div>
    </div>

    <nav class="menu">
  <a href="#book-appointment">Book Appointment</a>
  <a href="#ar-skin-analysis">AR Skin Analysis</a>
  <a href="#treatments">Treatment and Services</a>
  <a href="#shop">Shop</a>
  <a href="#reviews">Reviews</a>
  <a href="#locations">Location</a>
</nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <div class="top-actions">
      </div>
      <a class="login-pill" a href="index.php?login=true">Log in/Sign up</a>
    </header>

    <section id="book-appointment" class="hero">
      <h4 class="pre">Welcome to</h4>
      <h1 class="title">Skin Medic</h1>
      <p class="subtitle">Your journey to radiant, healthy skin begins here</p>

      <div class="hero-cta">
        <a class="cta cta-primary" href="#">Book a Session</a>
        <a class="cta cta-secondary" href="#">AR Skin Analysis</a>
      </div>
    </section>

<!-- LOGIN POPUP -->
<div id="loginPopup" class="popup">
  <div class="popup-content">
    <div class="popup-left">
      <img src="skinmedic-logo.png" alt="Skin Medic Logo" class="logo">
      <h2>Skin Medic</h2>
      <p>A Complete Skin Care Clinic</p>
    </div>

    <div class="popup-right">
      <span class="close" onclick="closePopup()">&times;</span>
      <h3>Client Login</h3>

     <form action="login_signup.php" method="POST">

        <div class="input-group">
          <label for="email">Email:</label>
          <input type="text" id="email" name="email" required>
        </div>
        <div class="input-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="login-btn">Login</button>
        <button type="button" class="create-btn">Create an Account</button>
      </form>

      <button class="admin-btn">Are you an admin?</button>
    </div>
  </div>
</div>

<style>
.popup {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 9999;
  font-family: 'Poppins', sans-serif;
}

.popup-content {
  display: flex;
  background: #f3e8df;
  border-radius: 18px;
  box-shadow: 0 6px 14px rgba(0,0,0,0.25);
  overflow: hidden;
  width: 880px; /* increased from 750px */
  max-width: 95%;
  min-height: 480px; /* slightly taller for breathing room */
}

.popup-left {
  flex: 1.1;
  background: #d7c4b4;
  color: #2f2a27;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  text-align: center;
}

.popup-left .logo {
  width: 120px;
  margin-bottom: 15px;
}

.popup-right {
  flex: 1.2;
  padding: 40px 80px 30px;
  position: relative;
  background: white;
}

.close {
  position: absolute;
  right: 25px;
  top: 20px;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.login-form {
  display: flex;
  flex-direction: column;
  margin-top: 25px;
  gap: 22px;
}

.input-group {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 18px;
}

.input-group label {
  width: 95px;
  text-align: right;
  color: #2f2a27;
  font-weight: 500;
}

.input-group input {
  flex: 1;
  padding: 12px 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 15px;
  max-width: 320px;
}

.login-btn {
  background: #80a833;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.2s;
  width: 100%;
}

.login-btn:hover {
  background: #6b8f28;
}

.create-btn {
  background: none;
  border: 1px solid #80a833;
  color: #80a833;
  padding: 12px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.2s;
  width: 100%;
}

.create-btn:hover {
  background: #80a833;
  color: white;
}

.admin-btn {
  background: none;
  border: none;
  color: #555;
  font-size: 14px;
  text-align: right;
  cursor: pointer;
  position: absolute;
  bottom: 25px;
  right: 40px;
}

.admin-btn:hover {
  color: #80a833;
}
</style>

<script>
function closePopup() {
  document.getElementById('loginPopup').style.display = 'none';
}
</script>



    <!-- AR card -->
    <section id="ar-skin-analysis" class="ar-card">
      <div class="ar-left">
        <h2>AR Skin Analysis</h2>
        <p class="lead">
          Experience our advanced AR skin analysis technology. Get instant insights about your skin type,
          concerns, and personalized treatment recommendations.
        </p>
        <ul class="ar-list">
          <li>Instant skin type detection</li>
          <li>Identify skin concerns and issues</li>
          <li>Personalized treatment recommendations</li>
        </ul>
        <div class="ar-cta-row">
          <a class="small-pill" href="#">Start Analysis</a>
        </div>
      </div>

      <div class="ar-right">
        <div class="ar-image">
          <img src="skin-analysis.jpg" alt="Skin analysis" onerror="this.src='https://i.ibb.co/FhGvxFY/skin-analysis.jpg'"/>
        </div>
      </div>
    </section>

    <!-- Signature treatments -->
    <section id="treatments" class="section treatments-section">
      <div class="section-header">
        <p class="kicker">OUR EXPERTISE</p>
        <h2 class="section-title">Signature Treatments</h2>
        <p class="section-sub">DISCOVER TRANSFORMATIVE TREATMENTS DESIGNED FOR YOUR UNIQUE SKINCARE NEEDS</p>
      </div>

      <div class="treatments-grid">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <article class="treatment-card">
  <div class="treatment-image">
    <img src="image/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
  </div>
  <h3><?= htmlspecialchars($row['name']) ?></h3>
  <p><?= htmlspecialchars($row['description']) ?></p>
  <p class="price">₱<?= number_format($row['price'], 2) ?></p>
</article>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No services found.</p>
  <?php endif; ?>
      </div>

      <div class="center-btn">
        <a class="view-all" href="#">View All Treatment</a>
      </div>
    </section>

    <!-- Shop -->
    <section id="shop" class="shop-products">
      <div class="section-header">
        <p class="kicker">OUR PRODUCTS</p>
        <h2 class="section-title">PRODUCTS</h2>
        <p class="section-sub">DESC</p>
      </div>

      <div class="shop-grid">
        <?php for($i=0;$i<6;$i++): ?>
          <article class="shop-card">
            <div class="shop-image">
              <img src="image/" alt="shop" onerror="this.src='https://i.ibb.co/2s7sG4v/placeholder.png'"/>
            </div>
            <h3>Product <?= $i+1 ?></h3>
          </article>
        <?php endfor; ?>
      </div>

      <div class="center-btn">
        <a class="view-all" href="#">View All Products</a>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="reviews" class="section reviews-section">
      <div class="reviews-panel">
        <h3 class="kicker">TESTIMONIALS</h3>
        <h2 class="section-title">Client Stories</h2>
        <p class="small">Hear from those who have experienced transformative results</p>

        <div class="reviews-grid">
          <?php
          $people = [
            ['name'=>'Maria Santos','text'=>'The anti-aging facial treatment exceeded my expectations!'],
            ['name'=>'Sarah Chen','text'=>'The Vitamin C serum is incredible!'],
            ['name'=>'John Reyes','text'=>'Amazing results from the Pico laser treatment!']
          ];
          foreach($people as $p): ?>
            <div class="review-card">
              <div class="stars">★★★★★</div>
              <p class="review-text">"<?php echo $p['text']; ?>"</p>
              <div class="author">
                <span class="avatar"><?php echo strtoupper($p['name'][0]); ?></span>
                <div>
                  <strong><?php echo $p['name']; ?></strong>
                  <div class="role">Treatment</div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="center-btn"><a class="outline-btn" href="#">Read More Stories</a></div>
      </div>
    </section>

    <!-- Locations -->
    <section id="locations" class="section locations-section">
      <div class="section-header center">
        <h2 class="section-title">Our Clinic Locations</h2>
        <p class="section-sub">Visit any of our convenient locations across Cavite for premium skincare treatments and consultations.</p>
      </div>

      <div class="location-card">
        <div class="loc-left">
          <h3>Skin Medic - Imus Branch <span class="badge">Premium Location</span></h3>
          <div class="loc-meta">
            <p><strong>Address:</strong> 123 General Trias Drive, Imus City, Cavite 4103</p>
            <p><strong>Phone:</strong> +63 46 123 4567</p>
            <p><strong>Hours:</strong> Mon-Fri: 9:00 AM - 7:00 PM</p>
          </div>
          <div class="loc-actions">
            <a class="directions" href="#">Get Directions</a>
            <a class="call" href="tel:+63461234567">Call Now</a>
          </div>
        </div>
        <div class="loc-right">
          <img src="https://images.unsplash.com/photo-1600180758890-cc2f27b46f1b?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=3a8f3d3e2f6f9c1f0a3b6c8d8f6a" alt="clinic interior"/>
        </div>
      </div>
    </section>

    <footer class="site-footer">
      <p>© <?= $year ?> Skin Medic. All rights reserved.</p>
    </footer>
  </main>

</body>
</html>