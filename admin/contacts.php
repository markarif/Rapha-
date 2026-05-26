<?php
require_once '../includes/config.php';
requireAdmin();

// Delete
if (isset($_GET['delete']) && $pdo) {
    $pdo->prepare("DELETE FROM contacts WHERE id=?")->execute([intval($_GET['delete'])]);
    flashSet('success', 'Message deleted.');
    redirect('/admin/contacts.php');
}

$messages = [];
$dbError  = null;
if (!$pdo) {
    $dbError = 'Database connection unavailable. Check config.php credentials.';
} else {
    try {
        $messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
    } catch (Exception $e) {
        $dbError = 'Could not load messages: ' . $e->getMessage();
    }
}
$flash    = flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Messages | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem;color:var(--green-900);">📩 Contact Messages</h1>
    <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
      <span style="color:var(--gray-600);font-size:0.88rem;"><?= count($messages) ?> total</span>
      <a href="/admin/export.php?type=contacts&format=csv" class="btn-sm" style="background:#2e7d32;color:#fff;padding:0.35rem 0.9rem;border-radius:6px;font-size:0.8rem;font-weight:600;text-decoration:none;">⬇ CSV</a>
      <a href="/admin/export.php?type=contacts&format=pdf" target="_blank" class="btn-sm" style="background:#F9A825;color:#333;padding:0.35rem 0.9rem;border-radius:6px;font-size:0.8rem;font-weight:600;text-decoration:none;">🖨 PDF</a>
    </div>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($dbError): ?>
  <div class="flash flash-error"><?= htmlspecialchars($dbError) ?></div>
  <?php endif; ?>

  <div class="admin-card">
    <?php if ($messages): ?>
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <?php foreach ($messages as $m): ?>
      <div style="border:1px solid var(--gray-200);border-radius:10px;padding:1.25rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;">
          <div>
            <strong style="color:var(--green-900);"><?= sanitize($m['name']) ?></strong>
            <span style="color:var(--gray-600);font-size:0.85rem;margin-left:0.75rem;"><?= sanitize($m['email']) ?></span>
            <?php if ($m['phone']): ?>
            <span style="color:var(--gray-600);font-size:0.85rem;margin-left:0.75rem;"><?= sanitize($m['phone']) ?></span>
            <?php endif; ?>
          </div>
          <div style="display:flex;align-items:center;gap:1rem;">
            <span style="color:var(--gray-600);font-size:0.82rem;"><?= date('d M Y H:i', strtotime($m['created_at'])) ?></span>
            <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Delete this message?')" style="color:#c62828;font-size:0.82rem;font-weight:600;">Delete</a>
          </div>
        </div>
        <?php if ($m['subject']): ?>
        <div style="font-weight:600;color:#333;margin-bottom:0.5rem;font-size:0.92rem;">Re: <?= sanitize($m['subject']) ?></div>
        <?php endif; ?>
        <p style="color:#555;font-size:0.92rem;line-height:1.7;"><?= nl2br(sanitize($m['message'])) ?></p>
        <a href="mailto:<?= sanitize($m['email']) ?>?subject=Re: <?= urlencode($m['subject'] ?: 'Your Enquiry') ?>"
           style="display:inline-block;margin-top:0.75rem;color:var(--green-700);font-weight:600;font-size:0.88rem;">↩ Reply by Email</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--gray-600);">No messages yet.</p>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
