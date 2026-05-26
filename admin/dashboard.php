<?php
require_once '../includes/config.php';
requireAdmin();

$stats = [
    'applications' => $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn(),
    'new_apps'     => $pdo->query("SELECT COUNT(*) FROM applications WHERE status='new'")->fetchColumn(),
    'gallery'      => $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn(),
    'news'         => $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn(),
    'contacts'     => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
    'team'         => $pdo->query("SELECT COUNT(*) FROM team")->fetchColumn(),
];
$recentContacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentNews     = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Admin | <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

<?php include 'includes/sidebar.php'; ?>

<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem; color:var(--green-900);">Dashboard</h1>
    <div style="display:flex; gap:1rem; align-items:center;">
      <span style="color:var(--gray-600); font-size:0.88rem;">Welcome, <?= sanitize($_SESSION['admin_user']) ?></span>
      <a href="/admin/logout.php" style="color:#c62828; font-size:0.85rem; font-weight:600;">Logout</a>
    </div>
  </div>

  <!-- Stats -->
  <div class="grid-4" style="margin-bottom:2rem;">
    <div class="stat-card" style="border-top:3px solid #1565c0;">
      <div style="font-size:2rem; margin-bottom:0.5rem;">📋</div>
      <div class="stat-num" style="color:#1565c0;"><?= $stats['applications'] ?></div>
      <div class="stat-label">Applications <span style="background:#e3f2fd;color:#1565c0;font-size:0.72rem;padding:0.1rem 0.5rem;border-radius:10px;font-weight:700;"><?= $stats['new_apps'] ?> new</span></div>
      <a href="/admin/applications.php" style="color:var(--green-700); font-size:0.82rem; font-weight:600; display:inline-block; margin-top:0.75rem;">View →</a>
    </div>
    <div class="stat-card">
      <div style="font-size:2rem; margin-bottom:0.5rem;">📸</div>
      <div class="stat-num"><?= $stats['gallery'] ?></div>
      <div class="stat-label">Gallery Photos</div>
      <a href="/admin/gallery.php" style="color:var(--green-700); font-size:0.82rem; font-weight:600; display:inline-block; margin-top:0.75rem;">Manage →</a>
    </div>
    <div class="stat-card">
      <div style="font-size:2rem; margin-bottom:0.5rem;">👥</div>
      <div class="stat-num"><?= $stats['team'] ?></div>
      <div class="stat-label">Team Members</div>
      <a href="/admin/team.php" style="color:var(--green-700); font-size:0.82rem; font-weight:600; display:inline-block; margin-top:0.75rem;">Manage →</a>
    </div>
    <div class="stat-card">
      <div style="font-size:2rem; margin-bottom:0.5rem;">📩</div>
      <div class="stat-num"><?= $stats['contacts'] ?></div>
      <div class="stat-label">Contact Messages</div>
      <a href="/admin/contacts.php" style="color:var(--green-700); font-size:0.82rem; font-weight:600; display:inline-block; margin-top:0.75rem;">View →</a>
    </div>
  </div>

  <div class="grid-2" style="gap:1.5rem;">

    <!-- Recent Messages -->
    <div class="admin-card">
      <h2>Recent Messages</h2>
      <?php if ($recentContacts): ?>
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Subject</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($recentContacts as $c): ?>
          <tr>
            <td><?= sanitize($c['name']) ?></td>
            <td style="color:var(--gray-600);"><?= sanitize($c['subject'] ?: 'General') ?></td>
            <td style="color:var(--gray-600); font-size:0.82rem;"><?= date('d M', strtotime($c['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <a href="/admin/contacts.php" style="color:var(--green-700); font-size:0.85rem; font-weight:600; display:inline-block; margin-top:1rem;">View all messages →</a>
      <?php else: ?>
      <p style="color:var(--gray-600); font-size:0.9rem;">No messages yet.</p>
      <?php endif; ?>
    </div>

    <!-- Recent News -->
    <div class="admin-card">
      <h2>Recent News Posts</h2>
      <?php if ($recentNews): ?>
      <table class="admin-table">
        <thead><tr><th>Title</th><th>Category</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($recentNews as $n): ?>
          <tr>
            <td><?= sanitize(substr($n['title'], 0, 40)) ?><?= strlen($n['title']) > 40 ? '...' : '' ?></td>
            <td><span class="badge badge-<?= sanitize($n['category']) ?>"><?= ucfirst(sanitize($n['category'])) ?></span></td>
            <td style="color:var(--gray-600); font-size:0.82rem;"><?= date('d M', strtotime($n['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <a href="/admin/news.php" style="color:var(--green-700); font-size:0.85rem; font-weight:600; display:inline-block; margin-top:1rem;">View all posts →</a>
      <?php else: ?>
      <p style="color:var(--gray-600); font-size:0.9rem;">No news posts yet.</p>
      <?php endif; ?>
    </div>

  </div>

  <!-- Quick links -->
  <div class="admin-card" style="margin-top:1.5rem;">
    <h2>Quick Actions</h2>
    <div style="display:flex; flex-wrap:wrap; gap:1rem;">
      <a href="/admin/gallery.php?action=upload" class="btn-primary">📸 Upload Photos</a>
      <a href="/admin/news.php?action=new" class="btn-primary">📝 Write News Post</a>
      <a href="/admin/fees.php?action=new" class="btn-primary">💰 Add Fee Record</a>
      <a href="/" target="_blank" class="btn-green">🌐 View Website</a>
    </div>
  </div>
</main>

</body>
</html>
