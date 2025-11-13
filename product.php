<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$query = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($query);
?>

<div class="main">
  <style>
    .products-container {
      padding: 20px 40px;
    }

    .products-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .products-header h2 {
      font-family: 'Playfair Display', serif;
      font-size: 42px;
      color: #2f2a27;
      margin: 0;
    }

    .products-header p {
      font-size: 15px;
      color: #666;
      font-style: italic;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-top: 30px;
    }

    .product-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      overflow: hidden;
      text-align: center;
      padding-bottom: 15px;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .product-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: 18px 18px 0 0;
    }

    .product-name {
      font-weight: 600;
      font-size: 18px;
      margin-top: 10px;
      color: #2f2a27;
    }

    .product-price {
      color: #80a833;
      font-weight: bold;
      margin-top: 5px;
    }

    .product-description {
      font-size: 14px;
      color: #666;
      padding: 10px 15px 0;
      min-height: 60px;
    }

    .no-products {
      text-align: center;
      color: #7b6e65;
      margin-top: 40px;
      font-style: italic;
    }
  </style>

  <div class="products-container">
    <div class="products-header">
      <h2>Our Skincare Products</h2>
      <p>Products are available for purchase at the clinic only.</p>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="product-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="product-card">
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="product-name"><?= htmlspecialchars($row['name']) ?></div>
            <div class="product-price">₱<?= number_format($row['price'], 2) ?></div>
            <div class="product-description"><?= htmlspecialchars($row['description']) ?></div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="no-products">No products available at the moment.</p>
    <?php endif; ?>
  </div>
</div>
