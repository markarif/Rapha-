<?php
require_once '../includes/config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$flash  = flashGet();

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $title    = trim($_POST['title']    ?? '');
    $category = trim($_POST['category'] ?? 'general');
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $uploaded = 0;

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $mime = mime_content_type($tmp);
        if (!in_array($mime, $allowed)) continue;

        $ext      = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
        $filename = uniqid('gallery_', true) . '.' . strtolower($ext);
        $dest     = UPLOADS_DIR . 'gallery/' . $filename;

        if (move_uploaded_file($tmp, $dest)) {
            $stmt = $pdo->prepare("INSERT INTO gallery (title, image_path, category, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$title ?: 'School Photo', $filename, $category]);
            $uploaded++;
        }
    }
    flashSet('success', "$uploaded photo(s) uploaded successfully.");
    redirect('/admin/gallery.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id   = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    if ($row) {
        $file = UPLOADS_DIR . 'gallery/' . $row['image_path'];
        if (file_exists($file)) unlink($file);
        $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
        flashSet('success', 'Photo deleted.');
    }
    redirect('/admin/gallery.php');
}

$photos = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll();
$flash  = $flash ?? flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gallery | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem; color:var(--green-900);">📸 Gallery Manager</h1>
    <a href="/admin/dashboard.php" style="color:var(--gray-600); font-size:0.88rem;">← Dashboard</a>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <!-- Upload Form -->
  <div class="admin-card">
    <h2>Upload Photos</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="grid-2" style="gap:1rem; margin-bottom:1rem;">
        <div class="form-group" style="margin:0;">
          <label>Photo Title / Caption (optional)</label>
          <input type="text" name="title" placeholder="e.g. Sports Day 2025">
        </div>
        <div class="form-group" style="margin:0;">
          <label>Category</label>
          <select name="category">
            <option value="general">General</option>
            <option value="classroom">Classroom</option>
            <option value="sports">Sports</option>
            <option value="events">Events</option>
            <option value="facilities">Facilities</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Select Photos (JPG, PNG, WEBP — multiple allowed)</label>
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"
               style="border:2px dashed var(--green-300);background:var(--green-50);padding:1.5rem;border-radius:8px;width:100%;cursor:pointer;">
      </div>
      <button type="submit" class="btn-primary">⬆ Upload Photos</button>
    </form>
  </div>

  <!-- Photo Grid -->
  <div class="admin-card">
    <h2>All Photos (<?= count($photos) ?>)</h2>
    <?php if ($photos): ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;">
      <?php foreach ($photos as $p): ?>
      <div style="position:relative;border-radius:8px;overflow:hidden;box-shadow:var(--shadow);">
        <img src="<?= sanitize(UPLOADS_URL . 'gallery/' . $p['image_path']) ?>"
             alt="<?= sanitize($p['title']) ?>"
             style="width:100%;height:140px;object-fit:cover;">
        <div style="padding:0.5rem 0.75rem;background:#fff;">
          <div style="font-size:0.78rem;font-weight:600;color:var(--green-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($p['title']) ?></div>
          <div style="font-size:0.72rem;color:var(--gray-600);"><?= sanitize($p['category']) ?></div>
        </div>
        <a href="/admin/gallery.php?delete=<?= $p['id'] ?>"
           onclick="return confirm('Delete this photo?')"
           style="position:absolute;top:6px;right:6px;background:rgba(198,40,40,0.85);color:#fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:700;">✕</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--gray-600);">No photos uploaded yet. Use the form above to add your first photos.</p>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
