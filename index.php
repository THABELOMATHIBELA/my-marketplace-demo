<?php
session_start();
require __DIR__ . '/inc/db.php';

// get cart count
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartCount += (int)$qty;
    }
}

// fetch products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Marketplace</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Inter font (you added) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- main styles -->
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- modern header -->
  <div class="header">
    <div class="container" style="display:flex;align-items:center;gap:16px;justify-content:space-between;padding:6px 0;">
      <a class="brand" href="index.php" style="text-decoration:none;">
        <div class="logo">MA</div>
        <div>
          <h1 style="margin:0">My Marketplace</h1>
          <p style="margin:0;font-size:12px;color:var(--muted)">Local demo store</p>
        </div>
      </a>

      <div class="search" aria-hidden="false">
        <input id="site-search" type="search" placeholder="Search products, e.g. headphones or powerbank...">
      </div>

      <div class="top-actions">
        <a href="admin/add_product.php" class="btn primary" title="Add product" style="text-decoration:none">
          Add
        </a>

        <a href="cart.php" class="btn" title="Cart" id="cart-button" style="text-decoration:none">
          <span class="cart-bubble" id="cart-count"><?php echo (int)$cartCount; ?></span>
        </a>
      </div>
    </div>
  </div>

  <div class="container">
    <h2 style="margin-top:18px">Featured Products</h2>

    <div class="grid" id="product-grid">
      <?php foreach ($products as $p): ?>
        <div class="card" aria-label="<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>">
          <!-- optional ribbon: uncomment if you want per-product ribbons -->
          <!-- <div class="ribbon">NEW</div> -->

          <a class="media" href="product.php?id=<?php echo (int)$p['id']; ?>">
            <img src="<?php echo htmlspecialchars($p['image'] ?: 'images/default.png', ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>">
          </a>

          <div>
            <div class="kicker"><?php echo htmlspecialchars(mb_substr($p['name'], 0, 20), ENT_QUOTES); ?></div>
            <h3><?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?></h3>
            <p class="small"><?php echo htmlspecialchars(mb_substr($p['description'], 0, 90), ENT_QUOTES); ?>…</p>
          </div>

          <div class="row">
            <div>
              <div class="price">M<?php echo number_format((float)$p['price'], 2); ?></div>
              <div class="small mt-8">Stock: <?php echo (int)$p['stock']; ?></div>
            </div>

            <div class="px-12 center">
              <button class="btn-add" type="button" onclick="addToCart(<?php echo (int)$p['id']; ?>)">Add</button>
              <a class="btn-ghost" href="product.php?id=<?php echo (int)$p['id']; ?>">View</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (empty($products)): ?>
        <p class="small mt-8">No products found. Use the <a class="btn secondary" href="admin/add_product.php">Add product</a> page to create some.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="footer">Demo marketplace — local only</div>

  <script src="js/app.js"></script>
</body>
</html>
