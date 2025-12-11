<?php
session_start();
require __DIR__ . '/inc/db.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

// build list and total (safe for empty ids)
$ids = array_keys($cart);
$products = [];
$total = 0;

if (!empty($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as &$p) {
        $p['qty'] = $cart[(int)$p['id']] ?? 0;
        $total += ((float)$p['price']) * (int)$p['qty'];
    }
    unset($p);
}

// handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '' || $email === '') {
        $error = "Please provide name and email.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, email, address, total) VALUES (?,?,?,?)");
        $stmt->execute([$name, $email, $address, $total]);
        $orderId = $pdo->lastInsertId();
        // clear cart
        unset($_SESSION['cart']);
        header("Location: checkout.php?success=1&id=" . (int)$orderId);
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Checkout — My Marketplace</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Optional Inter font -->
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

      <div class="top-actions">
        <a href="cart.php" class="btn secondary" style="text-decoration:none">Back to cart</a>
        <a href="index.php" class="btn" style="text-decoration:none">Continue shopping</a>
      </div>
    </div>
  </div>

  <div class="container" style="margin-top:20px;">
    <?php if (isset($_GET['success'])): ?>
      <div class="card">
        <h2>Order Placed</h2>
        <p>Thank you — your order #<?php echo (int)$_GET['id']; ?> has been received.</p>
        <div style="margin-top:12px">
          <a class="btn" href="index.php">Continue shopping</a>
        </div>
      </div>
    <?php else: ?>
      <div class="card" style="padding:18px;">
        <h2>Checkout</h2>

        <?php if (!empty($error)): ?>
          <p style="color:var(--danger);font-weight:700;"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></p>
        <?php endif; ?>

        <form method="post" action="checkout.php" style="display:grid;gap:12px;">
          <label>
            Name<br>
            <input name="name" required style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);">
          </label>

          <label>
            Email<br>
            <input name="email" type="email" required style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);">
          </label>

          <label>
            Address<br>
            <textarea name="address" rows="4" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);"></textarea>
          </label>

          <h3>Order Summary</h3>
          <ul>
            <?php foreach ($products as $p): ?>
              <li><?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?> x <?php echo (int)$p['qty']; ?> — M<?php echo number_format((float)$p['price'] * (int)$p['qty'], 2); ?></li>
            <?php endforeach; ?>
          </ul>

          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div style="font-weight:800;font-size:18px;">Total: M<?php echo number_format($total,2); ?></div>
            <button class="btn" type="submit" style="background:linear-gradient(90deg,var(--primary),var(--accent));border:none;color:#fff;padding:10px 14px;border-radius:10px;">Place order (demo)</button>
          </div>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <div class="footer">Demo marketplace — local only</div>

  <script src="js/app.js"></script>
</body>
</html>
