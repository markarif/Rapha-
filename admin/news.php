<?php
require_once '../includes/config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';
$editId = intval($_GET['edit'] ?? 0);
$flash  = flashGet();

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['image']) {
        $f = UPLOADS_DIR . $row['image'];
        if (file_exists($f)) unlink($f);
    }
    $pdo->prepare("DELETE FROM news WHERE id=?")->execute([$id]);
    flashSet('success', 'Post deleted.');
    redirect('/admin/news.php');
}

// Save (create or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = intval($_POST['id'] ?? 0);
    $title    = trim($_POST['title']    ?? '');
    $category = trim($_POST['category'] ?? 'news');
    $excerpt  = trim($_POST['excerpt']  ?? '');
    $content  = trim($_POST['content']  ?? '');
    $imageFile = null;

    if (!$title || !$content) {
        flashSet('error', 'Title and content are required.');
        redirect('/admin/news.php?action=new' . ($id ? '&edit=' . $id : ''));
    }

    // Handle image upload
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg','image/png','image/webp'];
        $mime    = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($mime, $allowed)) {
            $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageFile = uniqid('news_', true) . '.' . strtolower($ext);
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_DIR . $imageFile);
        }
    }

    if ($id) {
        // Update
        if ($imageFile) {
            $pdo->prepare("UPDATE news SET title=?,category=?,excerpt=?,content=?,image=?,updated_at=NOW() WHERE id=?")
                ->execute([$title, $category, $excerpt, $content, $imageFile, $id]);
        } else {
            $pdo->prepare("UPDATE news SET title=?,category=?,excerpt=?,content=?,updated_at=NOW() WHERE id=?")
                ->execute([$title, $category, $excerpt, $content, $id]);
        }
        flashSet('success', 'Post updated.');
    } else {
        $pdo->prepare("INSERT INTO news (title,category,excerpt,content,image,created_at) VALUES (?,?,?,?,?,NOW())")
            ->execute([$title, $category, $excerpt, $content, $imageFile]);
        flashSet('success', 'Post published.');
    }
    redirect('/admin/news.php');
}

// Load for editing
$editPost = null;
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id=?");
    $stmt->execute([$editId]);
    $editPost = $stmt->fetch();
    $action = 'new';
}

$posts = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
$flash = $flash ?? flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>News | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem;color:var(--green-900);">📰 News &amp; Events</h1>
    <div style="display:flex;gap:1rem;">
      <?php if ($action !== 'new'): ?>
      <a href="?action=new" class="btn-primary btn-sm">+ New Post</a>
      <?php else: ?>
      <a href="/admin/news.php" style="color:var(--gray-600);font-size:0.88rem;">← Back to list</a>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($action === 'new'): ?>
  <!-- ── New / Edit Post ── -->
  <div class="admin-card">
    <h2><?= $editPost ? 'Edit Post' : 'New Post' ?></h2>
    <form method="POST" enctype="multipart/form-data">
      <?php if ($editPost): ?><input type="hidden" name="id" value="<?= $editPost['id'] ?>"><?php endif; ?>
      <div class="grid-2" style="gap:1rem;">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" required placeholder="Post title" value="<?= sanitize($editPost['title'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Category *</label>
          <select name="category">
            <?php foreach (['news'=>'News','events'=>'Events','notices'=>'Notices'] as $val=>$lbl): ?>
            <option value="<?= $val ?>" <?= ($editPost['category'] ?? 'news') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Short Excerpt (shown on cards)</label>
        <input type="text" name="excerpt" placeholder="One-line summary..." value="<?= sanitize($editPost['excerpt'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Full Content *</label>
        <textarea name="content" required style="min-height:250px;" placeholder="Write the full article here..."><?= sanitize($editPost['content'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Featured Image (optional)</label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
               style="border:2px dashed var(--green-300);background:var(--green-50);padding:1rem;border-radius:8px;width:100%;">
        <?php if (!empty($editPost['image'])): ?>
        <p style="font-size:0.82rem;color:var(--gray-600);margin-top:0.5rem;">Current: <?= sanitize($editPost['image']) ?> (leave blank to keep)</p>
        <?php endif; ?>
      </div>
      <div style="display:flex;gap:1rem;">
        <button type="submit" class="btn-primary"><?= $editPost ? '💾 Update Post' : '🚀 Publish Post' ?></button>
        <a href="/admin/news.php" class="btn-green">Cancel</a>
      </div>
    </form>
  </div>

  <?php else: ?>
  <!-- ── Posts List ── -->
  <div class="admin-card">
    <h2>All Posts (<?= count($posts) ?>)</h2>
    <?php if ($posts): ?>
    <table class="admin-table">
      <thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
        <tr>
          <td style="font-weight:500;"><?= sanitize(substr($p['title'],0,55)) ?><?= strlen($p['title'])>55?'...':'' ?></td>
          <td><span class="badge badge-<?= sanitize($p['category']) ?>"><?= ucfirst(sanitize($p['category'])) ?></span></td>
          <td style="color:var(--gray-600);font-size:0.82rem;"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
          <td>
            <a href="?action=new&edit=<?= $p['id'] ?>" style="color:var(--green-700);font-weight:600;font-size:0.85rem;margin-right:0.75rem;">Edit</a>
            <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this post?')" style="color:#c62828;font-weight:600;font-size:0.85rem;">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <p style="color:var(--gray-600);">No posts yet. Click "New Post" to get started.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
