<?php
require_once '../includes/config.php';
requireAdmin();

$flash  = flashGet();
$viewId = intval($_GET['view'] ?? 0);

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['id'])) {
    $id     = intval($_POST['id']);
    $status = $_POST['status'];
    $allowed = ['new','reviewed','accepted','rejected'];
    if (in_array($status, $allowed)) {
        $pdo->prepare("UPDATE applications SET status=? WHERE id=?")->execute([$status, $id]);
        flashSet('success', 'Application status updated.');
    }
    redirect('/admin/applications.php' . ($viewId ? '?view='.$viewId : ''));
}

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM applications WHERE id=?")->execute([intval($_GET['delete'])]);
    flashSet('success', 'Application deleted.');
    redirect('/admin/applications.php');
}

// Load single
$application = null;
if ($viewId) {
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id=?");
    $stmt->execute([$viewId]);
    $application = $stmt->fetch();
}

$filter = $_GET['status'] ?? '';
$validStatuses = ['new','reviewed','accepted','rejected'];
if ($filter && in_array($filter, $validStatuses)) {
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE status=? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT * FROM applications ORDER BY created_at DESC");
}
$applications = $stmt->fetchAll();

// Counts
$counts = ['all'=>0,'new'=>0,'reviewed'=>0,'accepted'=>0,'rejected'=>0];
$counts['all'] = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
foreach ($validStatuses as $s) {
    $counts[$s] = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE status=?") && ($st = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE status=?")) && $st->execute([$s]) ? $st->fetchColumn() : 0;
}
// simpler count query
foreach ($validStatuses as $s) {
    $st = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE status=?");
    $st->execute([$s]);
    $counts[$s] = $st->fetchColumn();
}

$flash = $flash ?? flashGet();

$statusColors = [
    'new'      => 'background:#e3f2fd;color:#1565c0',
    'reviewed' => 'background:#fff3e0;color:#e65100',
    'accepted' => 'background:#e8f5e9;color:#2e7d32',
    'rejected' => 'background:#ffebee;color:#c62828',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Applications | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem;color:var(--green-900);">📋 Admission Applications</h1>
    <?php if ($viewId): ?>
    <a href="/admin/applications.php" style="color:var(--gray-600);font-size:0.88rem;">← Back to list</a>
    <?php else: ?>
    <span style="color:var(--gray-600);font-size:0.88rem;"><?= $counts['all'] ?> total</span>
    <?php endif; ?>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($application): ?>
  <!-- ── Single Application View ── -->
  <div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
      <div>
        <h2 style="margin-bottom:0.25rem;">Application #<?= $application['id'] ?></h2>
        <span style="font-size:0.82rem;padding:0.2rem 0.75rem;border-radius:20px;font-weight:600;<?= $statusColors[$application['status']] ?>"><?= ucfirst($application['status']) ?></span>
      </div>
      <form method="POST" style="display:flex;gap:0.75rem;align-items:center;">
        <input type="hidden" name="id" value="<?= $application['id'] ?>">
        <select name="status" style="padding:0.5rem;border:1.5px solid var(--gray-200);border-radius:6px;">
          <?php foreach ($validStatuses as $s): ?>
          <option value="<?= $s ?>" <?= $application['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary btn-sm">Update Status</button>
      </form>
    </div>

    <div class="grid-2" style="gap:2rem;">
      <div>
        <h3 style="color:var(--green-900);margin-bottom:1rem;font-size:1rem;border-bottom:1px solid var(--gray-200);padding-bottom:0.5rem;">Student Details</h3>
        <table style="width:100%;font-size:0.92rem;">
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);width:140px;">Full Name</td><td style="font-weight:600;"><?= sanitize($application['student_name']) ?></td></tr>
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);">Date of Birth</td><td><?= $application['date_of_birth'] ? date('d M Y', strtotime($application['date_of_birth'])) : '—' ?></td></tr>
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);">Grade Applying</td><td style="font-weight:600;color:var(--green-900);"><?= sanitize($application['grade_applying'] ?: '—') ?></td></tr>
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);">Previous School</td><td><?= sanitize($application['previous_school'] ?: 'None / New entrant') ?></td></tr>
        </table>
      </div>
      <div>
        <h3 style="color:var(--green-900);margin-bottom:1rem;font-size:1rem;border-bottom:1px solid var(--gray-200);padding-bottom:0.5rem;">Parent / Guardian</h3>
        <table style="width:100%;font-size:0.92rem;">
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);width:140px;">Name</td><td style="font-weight:600;"><?= sanitize($application['parent_name']) ?></td></tr>
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);">Phone</td><td><a href="tel:<?= sanitize($application['parent_phone']) ?>" style="color:var(--green-700);font-weight:600;"><?= sanitize($application['parent_phone']) ?></a></td></tr>
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);">Email</td><td><?= $application['parent_email'] ? '<a href="mailto:'.sanitize($application['parent_email']).'" style="color:var(--green-700);">'.sanitize($application['parent_email']).'</a>' : '—' ?></td></tr>
          <tr><td style="padding:0.4rem 0;color:var(--gray-600);">Address</td><td><?= sanitize($application['address'] ?: '—') ?></td></tr>
        </table>
      </div>
    </div>

    <?php if ($application['additional_info']): ?>
    <div style="margin-top:1.5rem;">
      <h3 style="color:var(--green-900);margin-bottom:0.75rem;font-size:1rem;">Additional Information</h3>
      <p style="color:#555;background:var(--gray-100);padding:1rem;border-radius:8px;font-size:0.92rem;line-height:1.7;"><?= nl2br(sanitize($application['additional_info'])) ?></p>
    </div>
    <?php endif; ?>

    <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--gray-200);display:flex;gap:1rem;flex-wrap:wrap;">
      <p style="color:var(--gray-600);font-size:0.85rem;">Submitted: <?= date('d M Y, H:i', strtotime($application['created_at'])) ?></p>
      <?php if ($application['parent_email']): ?>
      <a href="mailto:<?= sanitize($application['parent_email']) ?>?subject=Your Application to <?= SITE_NAME ?>" class="btn-primary btn-sm">✉ Email Parent</a>
      <?php endif; ?>
      <a href="?delete=<?= $application['id'] ?>" onclick="return confirm('Delete this application permanently?')" style="color:#c62828;font-size:0.85rem;font-weight:600;align-self:center;">Delete</a>
    </div>
  </div>

  <?php else: ?>
  <!-- ── Summary Stats ── -->
  <div class="grid-4" style="margin-bottom:1.5rem;">
    <?php
    $statDefs = [
      ['label'=>'Total',    'key'=>'all',      'icon'=>'📋', 'color'=>'#1565c0'],
      ['label'=>'New',      'key'=>'new',      'icon'=>'🆕', 'color'=>'#1565c0'],
      ['label'=>'Accepted', 'key'=>'accepted', 'icon'=>'✅', 'color'=>'#2e7d32'],
      ['label'=>'Rejected', 'key'=>'rejected', 'icon'=>'❌', 'color'=>'#c62828'],
    ];
    foreach ($statDefs as $s):
    ?>
    <div class="stat-card">
      <div style="font-size:1.8rem;margin-bottom:0.4rem;"><?= $s['icon'] ?></div>
      <div class="stat-num" style="color:<?= $s['color'] ?>"><?= $counts[$s['key']] ?></div>
      <div class="stat-label"><?= $s['label'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Filter -->
  <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem;">
    <a href="/admin/applications.php" style="<?= !$filter?'background:var(--green-800);color:#fff':'background:var(--gray-100);color:#333' ?>;padding:0.4rem 1rem;border-radius:6px;font-weight:600;font-size:0.85rem;">All</a>
    <?php foreach ($validStatuses as $s): ?>
    <a href="?status=<?= $s ?>" style="<?= $filter===$s?'background:var(--green-800);color:#fff':'background:var(--gray-100);color:#333' ?>;padding:0.4rem 1rem;border-radius:6px;font-weight:600;font-size:0.85rem;"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
  </div>

  <!-- List -->
  <div class="admin-card">
    <?php if ($applications): ?>
    <table class="admin-table">
      <thead><tr><th>#</th><th>Student</th><th>Grade</th><th>Parent</th><th>Phone</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($applications as $a): ?>
        <tr>
          <td style="color:var(--gray-600);"><?= $a['id'] ?></td>
          <td style="font-weight:600;"><?= sanitize($a['student_name']) ?></td>
          <td><?= sanitize($a['grade_applying'] ?: '—') ?></td>
          <td><?= sanitize($a['parent_name']) ?></td>
          <td><a href="tel:<?= sanitize($a['parent_phone']) ?>" style="color:var(--green-700);"><?= sanitize($a['parent_phone']) ?></a></td>
          <td><span style="font-size:0.78rem;padding:0.2rem 0.6rem;border-radius:20px;font-weight:600;<?= $statusColors[$a['status']] ?>"><?= ucfirst($a['status']) ?></span></td>
          <td style="color:var(--gray-600);font-size:0.82rem;"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
          <td><a href="?view=<?= $a['id'] ?>" style="color:var(--green-700);font-weight:600;font-size:0.85rem;">View</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <p style="color:var(--gray-600);text-align:center;padding:2rem;">No applications yet.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
