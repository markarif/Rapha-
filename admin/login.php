<?php
require_once '../includes/config.php';

if (isAdminLoggedIn()) {
    redirect('/admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $user['username'];
        redirect('/admin/dashboard.php');
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
</head>
<body style="background:var(--green-900); min-height:100vh; display:flex; align-items:center; justify-content:center;">

<div style="background:#fff; border-radius:16px; padding:2.5rem; width:100%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
  <div style="text-align:center; margin-bottom:2rem;">
    <div class="logo-icon" style="margin:0 auto 1rem; width:56px; height:56px; font-size:1.2rem;">RG</div>
    <h2 style="color:var(--green-900); font-size:1.4rem;">Admin Panel</h2>
    <p style="color:var(--gray-600); font-size:0.88rem;">Rapha Garden School</p>
  </div>

  <?php if ($error): ?>
  <div class="flash flash-error"><?= sanitize($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus placeholder="admin">
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="••••••••">
    </div>
    <button type="submit" class="btn-primary" style="width:100%; margin-top:0.5rem;">Sign In</button>
  </form>

  <div style="text-align:center; margin-top:1.5rem;">
    <a href="/" style="color:var(--gray-600); font-size:0.85rem;">← Back to Website</a>
  </div>
</div>

</body>
</html>
