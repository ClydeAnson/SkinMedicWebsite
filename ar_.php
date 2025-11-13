<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AR Skin Analysis | SkinMedic</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  margin: 0;
  padding: 40px;
  color: #2f2a27;
}

/* Container */
.ar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 40px;
  background: linear-gradient(to bottom, #fff7e6, #f5d991);
  padding: 40px 60px;
  border-radius: 20px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  max-width: 1200px;
  margin: 0 auto;
}

.ar-text {
  flex: 1;
}
.ar-text h2 {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  color: #2f2a27;
  margin-bottom: 20px;
}
.ar-text p {
  font-size: 16px;
  line-height: 1.6;
  color: #3e3e3e;
  margin-bottom: 20px;
}
.ar-text ul {
  margin-left: 20px;
  margin-bottom: 25px;
}
.ar-text li {
  margin-bottom: 10px;
}

/* Button */
.start-btn {
  background: #80a833;
  color: white;
  border: none;
  padding: 12px 25px;
  border-radius: 25px;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: 0.3s;
}
.start-btn:hover {
  background: #6e8f28;
}

/* Image Section */
.ar-image {
  flex-shrink: 0;
  background: white;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  padding: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.ar-image:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.ar-image img {
  width: 330px;
  height: auto;
  border-radius: 14px;
  display: block;
}

/* Modal */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.6);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  padding: 30px;
  border-radius: 16px;
  width: 400px;
  text-align: center;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
  from {opacity: 0; transform: scale(0.9);}
  to {opacity: 1; transform: scale(1);}
}
.close-btn {
  background: #ccc;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  margin-top: 15px;
  cursor: pointer;
  transition: 0.3s;
}
.close-btn:hover {
  background: #999;
  color: white;
}

input[type="file"] {
  margin-top: 10px;
  border: 1px solid #ddd;
  padding: 10px;
  border-radius: 6px;
  width: 100%;
}

@media (max-width: 900px) {
  .ar-container { flex-direction: column; text-align: center; }
  .ar-image img { width: 250px; }
}
</style>
</head>
<body>

<div class="ar-container">
  <div class="ar-text">
    <h2>AR Skin Analysis</h2>
    <p>Experience our advanced AR skin analysis technology. Get instant insights about your skin type, concerns, and personalized treatment recommendations.</p>
    <ul>
      <li>Instant skin type detection</li>
      <li>Identify skin concerns and issues</li>
      <li>Personalized treatment recommendations</li>
    </ul>
    <button class="start-btn" onclick="openModal()">Start Analysis</button>
  </div>

  <div class="ar-image">
    <img src="ar_sample.jpg" alt="AR Skin Analysis">
  </div>
</div>

<!-- Modal -->
<div id="arModal" class="modal">
  <div class="modal-content">
    <h3>Upload Your Photo</h3>
    <p>Upload a clear photo of your face to begin analysis.</p>
    <input type="file" accept="image/*">
    <br>
    <button class="close-btn" onclick="closeModal()">Cancel</button>
  </div>
</div>

<script>
function openModal() {
  document.getElementById('arModal').style.display = 'flex';
}
function closeModal() {
  document.getElementById('arModal').style.display = 'none';
}
</script>

</body>
</html>
