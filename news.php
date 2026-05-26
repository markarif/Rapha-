<?php
require_once 'includes/config.php';
$pageTitle = "News & Events";
$pageDesc  = "Stay updated with the latest news, events and notices from Rapha Garden School.";

$category = $_GET['cat'] ?? '';
$id       = intval($_GET['id'] ?? 0);

// Single article view
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    if (!$article) { header('Location: /news'); exit; }
}

// List view
if (!$id) {
    $validCats = ['news', 'events', 'notices'];
    if ($category && in_array($category, $validCats)) {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    }
    $items = $stmt->fetchAll();
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / <a href="/news">News</a><?= $id ? ' / Article' : '' ?></div>
    <h1><?= $id ? sanitize($article['title']) : 'News &amp; Events' ?></h1>
    <p><?= $id ? date('d F Y', strtotime($article['created_at'])) : 'Latest updates from Rapha Garden School' ?></p>
  </div>
</div>

<section style="background:#fff;">
  <div class="container">

    <?php if ($id): ?>
    <!-- ── Single Article ── -->
    <div style="max-width:800px; margin:0 auto;">
      <span class="cat-badge cat-<?= sanitize($article['category']) ?>"><?= ucfirst(sanitize($article['category'])) ?></span>
      <?php if ($article['image']): ?>
      <img src="<?= sanitize(UPLOADS_URL . $article['image']) ?>" alt="<?= sanitize($article['title']) ?>" style="width:100%;border-radius:12px;margin:1.5rem 0;max-height:450px;object-fit:cover;">
      <?php endif; ?>
      <div style="font-size:1.05rem; line-height:1.9; color:#444;">
        <?= nl2br(sanitize($article['content'])) ?>
      </div>
      <div style="margin-top:2.5rem; padding-top:1.5rem; border-top:1px solid var(--gray-200);">
        <a href="/news" style="color:var(--green-700); font-weight:600;">← Back to News</a>
      </div>
    </div>

    <?php else: ?>
    <!-- ── News List ── -->

    <!-- Category filter -->
    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; justify-content:center; margin-bottom:3rem;">
      <a href="/news" style="<?= !$category ? 'background:var(--green-800);color:#fff;' : 'background:var(--gray-100);color:#333;' ?> padding:0.5rem 1.25rem;border-radius:6px;font-weight:600;">All</a>
      <?php
      $cats = ['news' => '📰 News', 'events' => '🗓 Events', 'notices' => '📌 Notices'];
      foreach ($cats as $slug => $label):
      $active = $category === $slug;
      ?>
      <a href="/news?cat=<?= $slug ?>" style="<?= $active ? 'background:var(--green-800);color:#fff;' : 'background:var(--gray-100);color:#333;' ?> padding:0.5rem 1.25rem;border-radius:6px;font-weight:600;"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($items)): ?>
    <div class="grid-3">
      <?php foreach ($items as $item): ?>
      <article class="card news-card">
        <?php if ($item['image']): ?>
          <img src="<?= sanitize(UPLOADS_URL . $item['image']) ?>" alt="<?= sanitize($item['title']) ?>" class="news-img">
        <?php else: ?>
          <div style="height:180px;background:var(--green-50);display:flex;align-items:center;justify-content:center;font-size:3rem;">
            <?= $item['category'] === 'events' ? '🗓' : ($item['category'] === 'notices' ? '📌' : '📰') ?>
          </div>
        <?php endif; ?>
        <div class="card-body">
          <span class="cat-badge cat-<?= sanitize($item['category']) ?>"><?= ucfirst(sanitize($item['category'])) ?></span>
          <p class="news-date"><?= date('d M Y', strtotime($item['created_at'])) ?></p>
          <h3><?= sanitize($item['title']) ?></h3>
          <p><?= sanitize(substr($item['excerpt'] ?? $item['content'], 0, 110)) ?>...</p>
          <a href="/news?id=<?= $item['id'] ?>" class="read-more">Read More →</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center; padding:4rem 0; color:var(--gray-600);">
      <div style="font-size:3.5rem; margin-bottom:1rem;">📰</div>
      <h3 style="color:var(--green-900); margin-bottom:0.5rem;">No posts yet</h3>
      <p>Check back soon for the latest news and updates from the school.</p>
    </div>
    <?php endif; ?>

    <?php endif; ?>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
