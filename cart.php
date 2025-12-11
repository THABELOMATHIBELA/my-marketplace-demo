<?php
session_start();
require __DIR__ . '/inc/db.php';

// Handle removal (POST) BEFORE output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $rid = (int)$_POST['remove_id'];
    if (isset($_SESSION['cart'][$rid])) {
        unset($_SESSION['cart'][$rid]);
    }
    header('Location: cart.php');
    exit;
}

// cart and cart count
$cart = $_SESSION['cart'] ?? [];
$cartCount = 0;
foreach ($cart as $q) $cartCount += (int)$q;

// prepare products list
$products = [];
$total = 0;
$ids = array_keys($cart);

if (!empty($ids)) {
    // build placeholders safely
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // attach qty and compute total
    foreach ($products as &$p) {
        $p['qty'] = $cart[(int)$p['id']] ?? 0;
        $total += ((float)$p['price']) * (int)$p['qty'];
    }
    unset($p);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Your Cart — My Marketplace</title>
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
        <a href="index.php" class="btn secondary" style="text-decoration:none">Continue shopping</a>
        <a href="cart.php" class="btn" id="cart-button" style="text-decoration:none">
          Cart <span class="cart-bubble" id="cart-count"><?php echo (int)$cartCount; ?></span>
        </a>
      </div>
    </div>
  </div>

  <div class="container" style="margin-top:20px;">
    <h2>Your Cart</h2>

    <?php if (empty($products)): ?>
      <div class="card">
        <p class="small">Your cart is empty.</p>
        <div style="margin-top:12px">
          <a class="btn" href="index.php">Continue shopping</a>
        </div>
      </div>
    <?php else: ?>
      <div class="card" style="padding:18px;">
        <table class="cart-list" style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="text-align:left;">
              <th style="padding:8px 6px;color:var(--muted)">Product</th>
              <th style="padding:8px 6px;color:var(--muted)">Qty</th>
              <th style="padding:8px 6px;color:var(--muted)">Price</th>
              <th style="padding:8px 6px;color:var(--muted)">Subtotal</th>
              <th style="padding:8px 6px;"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr>
                <td style="padding:10px 6px;vertical-align:middle;">
                  <div style="display:flex;gap:12px;align-items:center;">
                    <img src="<?php echo htmlspecialchars($p['image'] ?: 'images/default.png', ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>" style="width:72px;height:56px;object-fit:cover;border-radius:8px;">
                    <div>
                      <div style="font-weight:700;"><?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?></div>
                      <div class="small" style="color:var(--muted)"><?php echo htmlspecialchars(mb_substr($p['description'],0,60), ENT_QUOTES); ?>…</div>
                    </div>
                  </div>
                </td>
                <td style="padding:10px 6px;vertical-align:middle;"><?php echo (int)$p['qty']; ?></td>
                <td style="padding:10px 6px;vertical-align:middle;">M<?php echo number_format((float)$p['price'],2); ?></td>
                <td style="padding:10px 6px;vertical-align:middle;">M<?php echo number_format((float)$p['price'] * (int)$p['qty'],2); ?></td>
                <td style="padding:10px 6px;vertical-align:middle;">
                  <form method="post" action="cart.php" style="display:inline">
                    <input type="hidden" name="remove_id" value="<?php echo (int)$p['id']; ?>">
                    <button type="submit" class="btn" style="background:var(--danger);border:none;color:#fff;">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;">
          <div class="small">You have <strong><?php echo array_sum($cart); ?></strong> item(s) in your cart.</div>
          <div style="display:flex;gap:12px;align-items:center">
            <div style="font-weight:800;font-size:18px;">Total: M<?php echo number_format($total,2); ?></div>
            <a class="btn" href="checkout.php">Proceed to checkout</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="footer">Demo marketplace — local only</div>

  <script src="js/app.js"></script>
</body>
</html>
