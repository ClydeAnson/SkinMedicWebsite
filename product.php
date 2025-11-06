<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Fetch all products from database
$query = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store | SkinMedic</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3e8df;
            margin: 0;
            padding: 40px;
            color: #2f2a27;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2f2a27;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            padding: 15px;
            transition: 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .product-name {
            font-weight: 600;
            font-size: 18px;
            color: #2f2a27;
            margin-bottom: 5px;
        }

        .product-price {
            color: #80a833;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .buy-btn {
            background: #80a833;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .buy-btn:hover {
            background: #6b8f28;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            background: #b8a28a;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
        }

        .back-btn:hover {
            background: #9d8a74;
        }

        .no-products {
            text-align: center;
            font-style: italic;
            color: #7b6e65;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🛒 SkinMedic Store</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        <div class="product-name"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="product-price">₱<?= number_format($row['price'], 2) ?></div>
                        <div class="product-description"><?= htmlspecialchars($row['description']) ?></div>
                        <button class="buy-btn">Buy Now</button>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-products">No products available at the moment.</p>
        <?php endif; ?>

        <a href="patient_dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
</body>
</html>
