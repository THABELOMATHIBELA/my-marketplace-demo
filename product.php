<?php
session_start();
require __DIR__ . '/inc/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    // A nicer 404 message with link back to home
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Not found</title>';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<link rel="stylesheet" href="css/styles.css"></head><body>';
    echo '<div class="container"><h2>Product not found</h2><p>The product you requested does not exist.</p>';
    echo '<p><a class="btn" href="index.php">Back to shop</a></p></div></body></html>';
    exit;
}

// cart count
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $q) {
        $cartCount += (int)$q;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Optional Inter font (comment out if offline) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

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
        <a href="admin/add_product.php" class="btn secondary" title="Add product" style="text-decoration:none">
          <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" width="16" height="16"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Add
        </a>

        <a href="cart.php" class="btn" title="Cart" id="cart-button" style="text-decoration:none">
          <svg class="icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" width="16" height="16"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span class="cart-bubble" id="cart-count"><?php echo (int)$cartCount; ?></span>
        </a>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="product-details" style="margin-top:18px;">
      <div class="product-media">
        <img src="<?php echo htmlspecialchars($product['image'] ?: 'images/default.png', ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>">
      </div>

      <div>
        <div class="kicker">Product</div>
        <h1 style="margin-top:8px;"><?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?></h1>
        <p class="small" style="margin-top:6px;"><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES)); ?></p>

        <div class="mt-8">
          <div class="price" style="font-size:20px; margin-top:12px;">M<?php echo number_format((float)$product['price'], 2); ?></div>
          <div class="small mt-8">Stock: <?php echo (int)$product['stock']; ?></div>
        </div>

        <div style="margin-top:16px; display:flex;gap:12px;align-items:center;">
          <button class="btn-add" type="button" onclick="addToCart(<?php echo (int)$product['id']; ?>)">Add to cart</button>
          <a class="btn-ghost" href="cart.php">View cart</a>
        </div>
      </div>
    </div>
  </div>

  <div class="footer">Demo marketplace — local only</div>

  <script src="js/app.js"></script>
</body>
</html>
