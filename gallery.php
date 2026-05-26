<?php
require_once 'includes/config.php';
$pageTitle = "Gallery";
$pageDesc  = "Browse photos from Rapha Garden School — classroom life, sports, events and more.";

$category = $_GET['cat'] ?? '';
$validCats = ['classroom', 'sports', 'events', 'facilities'];

if ($category && in_array($category, $validCats)) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
}
$photos = $stmt->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Gallery</div>
    <h1>Photo Gallery</h1>
    <p>Moments from our vibrant school community</p>
  </div>
</div>

<section style="background:#fff;">
  <div class="container">

    <!-- Filter tabs -->
    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; justify-content:center; margin-bottom:3rem;">
      <a href="/gallery" class="btn-sm <?= !$category ? 'btn-green' : 'btn-outline-green' ?>" style="<?= !$category ? 'background:var(--green-800);color:#fff;padding:0.5rem 1.25rem;border-radius:6px;font-weight:600' : 'background:var(--gray-100);color:#333;padding:0.5rem 1.25rem;border-radius:6px;font-weight:600' ?>">All Photos</a>
      <?php
      $cats = ['classroom' => 'Classroom', 'sports' => 'Sports', 'events' => 'Events', 'facilities' => 'Facilities'];
      foreach ($cats as $slug => $label):
      $active = $category === $slug;
      ?>
      <a href="/gallery?cat=<?= $slug ?>" style="<?= $active ? 'background:var(--green-800);color:#fff;' : 'background:var(--gray-100);color:#333;' ?> padding:0.5rem 1.25rem;border-radius:6px;font-weight:600;"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($photos)): ?>
    <div class="gallery-grid">
      <?php foreach ($photos as $photo): ?>
      <div class="gallery-item">
        <img src="<?= sanitize(UPLOADS_URL . 'gallery/' . $photo['image_path']) ?>"
             alt="<?= sanitize($photo['title'] ?? 'School photo') ?>"
             loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- Empty state -->
    <div style="text-align:center; padding:5rem 0; color:var(--gray-600);">
      <div style="font-size:4rem; margin-bottom:1rem;">📸</div>
      <h3 style="color:var(--green-900); margin-bottom:0.5rem;">Photos Coming Soon</h3>
      <p>Our gallery is being updated. Check back soon for photos of school life at Rapha Garden.</p>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
  <button class="lightbox-close">&times;</button>
  <img id="lightboxImg" src="" alt="Photo">
</div>

<?php require_once 'includes/footer.php'; ?>
