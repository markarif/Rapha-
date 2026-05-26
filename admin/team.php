<?php
require_once '../includes/config.php';
requireAdmin();

$flash  = flashGet();
$action = $_GET['action'] ?? 'list';
$editId = intval($_GET['edit'] ?? 0);

// Delete
if (isset($_GET['delete'])) {
    $id   = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT photo FROM team WHERE id=?");
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    if ($row && $row['photo']) {
        $f = UPLOADS_DIR . 'gallery/' . $row['photo'];
        if (file_exists($f)) unlink($f);
    }
    $pdo->prepare("DELETE FROM team WHERE id=?")->execute([$id]);
    flashSet('success', 'Team member deleted.');
    redirect('/admin/team.php');
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = intval($_POST['id'] ?? 0);
    $name        = trim($_POST['name']        ?? '');
    $role        = trim($_POST['role']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon        = trim($_POST['icon']        ?? '👤');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $photo       = null;

    if (!$name || !$role) {
        flashSet('error', 'Name and Role are required.');
        redirect('/admin/team.php?action=new' . ($id ? '&edit='.$id : ''));
    }

    // Photo upload
    if (!empty($_FILES['photo']['tmp_name'])) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        $mime    = mime_content_type($_FILES['photo']['tmp_name']);
        if (in_array($mime, $allowed)) {
            $ext   = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo = uniqid('team_', true) . '.' . strtolower($ext);
            move_uploaded_file($_FILES['photo']['tmp_name'], UPLOADS_DIR . 'gallery/' . $photo);
        }
    }

    if ($id) {
        if ($photo) {
            $pdo->prepare("UPDATE team SET name=?,role=?,description=?,icon=?,photo=?,sort_order=? WHERE id=?")
                ->execute([$name,$role,$description,$icon,$photo,$sort_order,$id]);
        } else {
            $pdo->prepare("UPDATE team SET name=?,role=?,description=?,icon=?,sort_order=? WHERE id=?")
                ->execute([$name,$role,$description,$icon,$sort_order,$id]);
        }
        flashSet('success', 'Team member updated.');
    } else {
        $pdo->prepare("INSERT INTO team (name,role,description,icon,photo,sort_order,created_at) VALUES (?,?,?,?,?,?,NOW())")
            ->execute([$name,$role,$description,$icon,$photo,$sort_order]);
        flashSet('success', 'Team member added.');
    }
    redirect('/admin/team.php');
}

// Load for edit
$editRow = null;
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM team WHERE id=?");
    $stmt->execute([$editId]);
    $editRow = $stmt->fetch();
    $action  = 'new';
}

$members = $pdo->query("SELECT * FROM team ORDER BY sort_order ASC, id ASC")->fetchAll();
$flash   = $flash ?? flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Our Team | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem;color:var(--green-900);">👥 Our Team</h1>
    <?php if ($action !== 'new'): ?>
    <a href="?action=new" class="btn-primary btn-sm">+ Add Member</a>
    <?php else: ?>
    <a href="/admin/team.php" style="color:var(--gray-600);font-size:0.88rem;">← Back to list</a>
    <?php endif; ?>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($action === 'new'): ?>
  <div class="admin-card">
    <h2><?= $editRow ? 'Edit Team Member' : 'Add Team Member' ?></h2>
    <form method="POST" enctype="multipart/form-data">
      <?php if ($editRow): ?><input type="hidden" name="id" value="<?= $editRow['id'] ?>"><?php endif; ?>
      <div class="grid-2" style="gap:1rem;">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="name" required placeholder="e.g. Mr. John Kamau" value="<?= sanitize($editRow['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Role / Title *</label>
          <input type="text" name="role" required placeholder="e.g. School Principal" value="<?= sanitize($editRow['role'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Emoji Icon <small style="color:var(--gray-600)">(copy from emojipedia.org)</small></label>
          <input type="text" name="icon" placeholder="👨‍💼" value="<?= sanitize($editRow['icon'] ?? '👤') ?>" style="font-size:1.5rem; width:80px;">
        </div>
        <div class="form-group">
          <label>Display Order <small style="color:var(--gray-600)">(1 = first)</small></label>
          <input type="number" name="sort_order" min="0" value="<?= $editRow['sort_order'] ?? 0 ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" placeholder="Brief description of their role..." style="min-height:100px;"><?= sanitize($editRow['description'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Photo (optional — JPG, PNG, WEBP)</label>
        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
               style="border:2px dashed var(--green-300);background:var(--green-50);padding:1rem;border-radius:8px;width:100%;">
        <?php if (!empty($editRow['photo'])): ?>
        <p style="font-size:0.82rem;color:var(--gray-600);margin-top:0.5rem;">Current photo set. Upload a new one to replace.</p>
        <?php endif; ?>
      </div>
      <div style="display:flex;gap:1rem;">
        <button type="submit" class="btn-primary"><?= $editRow ? '💾 Update' : '➕ Add Member' ?></button>
        <a href="/admin/team.php" class="btn-green">Cancel</a>
      </div>
    </form>
  </div>

  <?php else: ?>
  <div class="admin-card">
    <h2>Team Members (<?= count($members) ?>)</h2>
    <p style="color:var(--gray-600);font-size:0.88rem;margin-bottom:1.25rem;">These appear on the About page. Use Display Order to control the sequence.</p>
    <?php if ($members): ?>
    <table class="admin-table">
      <thead><tr><th>Order</th><th>Icon</th><th>Name</th><th>Role</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($members as $m): ?>
        <tr>
          <td style="color:var(--gray-600);"><?= $m['sort_order'] ?></td>
          <td style="font-size:1.5rem;"><?= $m['icon'] ?></td>
          <td style="font-weight:600;"><?= sanitize($m['name']) ?></td>
          <td style="color:var(--gray-600);"><?= sanitize($m['role']) ?></td>
          <td>
            <a href="?action=new&edit=<?= $m['id'] ?>" style="color:var(--green-700);font-weight:600;font-size:0.85rem;margin-right:0.75rem;">Edit</a>
            <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Delete <?= sanitize($m['name']) ?>?')" style="color:#c62828;font-weight:600;font-size:0.85rem;">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <p style="color:var(--gray-600);">No team members yet. Click "Add Member" to get started.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
