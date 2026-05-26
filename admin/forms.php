<?php
require_once '../includes/config.php';
requireAdmin();

$flash = flashGet();

// Delete
if (isset($_GET['delete'])) {
    $id   = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT filename FROM forms WHERE id=?");
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    if ($row) {
        $f = UPLOADS_DIR . 'forms/' . $row['filename'];
        if (file_exists($f)) unlink($f);
        $pdo->prepare("DELETE FROM forms WHERE id=?")->execute([$id]);
        flashSet('success', 'Form deleted.');
    }
    redirect('/admin/forms.php');
}

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $level       = trim($_POST['level']       ?? '');

    if (!$title) {
        flashSet('error', 'Title is required.');
        redirect('/admin/forms.php');
    }

    if (empty($_FILES['form_file']['tmp_name'])) {
        flashSet('error', 'Please select a file to upload.');
        redirect('/admin/forms.php');
    }

    $allowed_mime = ['application/pdf', 'application/msword',
                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $mime = mime_content_type($_FILES['form_file']['tmp_name']);

    if (!in_array($mime, $allowed_mime)) {
        flashSet('error', 'Only PDF and Word documents are allowed.');
        redirect('/admin/forms.php');
    }

    $ext      = pathinfo($_FILES['form_file']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('form_', true) . '.' . strtolower($ext);
    move_uploaded_file($_FILES['form_file']['tmp_name'], UPLOADS_DIR . 'forms/' . $filename);

    $pdo->prepare("INSERT INTO forms (title,description,filename,level,created_at) VALUES (?,?,?,?,NOW())")
        ->execute([$title, $description, $filename, $level]);
    flashSet('success', 'Form uploaded successfully.');
    redirect('/admin/forms.php');
}

$forms = $pdo->query("SELECT * FROM forms ORDER BY created_at DESC")->fetchAll();
$flash = $flash ?? flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Forms | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem;color:var(--green-900);">📄 Download Forms</h1>
    <span style="color:var(--gray-600);font-size:0.88rem;"><?= count($forms) ?> form(s) uploaded</span>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <!-- Upload -->
  <div class="admin-card">
    <h2>Upload New Form</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="grid-2" style="gap:1rem;">
        <div class="form-group">
          <label>Form Title *</label>
          <input type="text" name="title" required placeholder="e.g. PP1 Admission Form 2025">
        </div>
        <div class="form-group">
          <label>Level / Category</label>
          <select name="level">
            <option value="">All Levels</option>
            <option value="Pre-Primary (PP1 & PP2)">Pre-Primary (PP1 &amp; PP2)</option>
            <option value="Primary (Grade 1–6)">Primary (Grade 1–6)</option>
            <option value="Junior Secondary (Grade 7–9)">Junior Secondary (Grade 7–9)</option>
            <option value="General">General</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Description (optional)</label>
        <input type="text" name="description" placeholder="e.g. Fill in and bring to school office">
      </div>
      <div class="form-group">
        <label>File (PDF or Word document) *</label>
        <input type="file" name="form_file" accept=".pdf,.doc,.docx" required
               style="border:2px dashed var(--green-300);background:var(--green-50);padding:1.25rem;border-radius:8px;width:100%;">
      </div>
      <button type="submit" class="btn-primary">⬆ Upload Form</button>
    </form>
  </div>

  <!-- List -->
  <div class="admin-card">
    <h2>All Forms (<?= count($forms) ?>)</h2>
    <?php if ($forms): ?>
    <table class="admin-table">
      <thead><tr><th>Title</th><th>Level</th><th>File</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($forms as $f): ?>
        <tr>
          <td style="font-weight:600;"><?= sanitize($f['title']) ?></td>
          <td style="color:var(--gray-600);"><?= sanitize($f['level'] ?: 'All') ?></td>
          <td>
            <a href="<?= UPLOADS_URL ?>forms/<?= sanitize($f['filename']) ?>" target="_blank"
               style="color:var(--green-700);font-weight:600;font-size:0.85rem;">⬇ Download</a>
          </td>
          <td style="color:var(--gray-600);font-size:0.82rem;"><?= date('d M Y', strtotime($f['created_at'])) ?></td>
          <td>
            <a href="?delete=<?= $f['id'] ?>" onclick="return confirm('Delete this form?')"
               style="color:#c62828;font-weight:600;font-size:0.85rem;">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <p style="color:var(--gray-600);">No forms uploaded yet.</p>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
