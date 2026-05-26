<?php
require_once 'includes/config.php';
$pageTitle = "Home";
$pageDesc  = "Rapha Garden School - Nurturing Excellence, Growing Futures. CBC-accredited private primary school in Athi River, Machakos County, Kenya.";

// Latest 3 news items
$news = $pdo ? $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3")->fetchAll() : [];

// Latest 6 gallery items
$gallery = $pdo ? $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6")->fetchAll() : [];

// Hero slideshow — up to 5 gallery photos
$heroSlides = $pdo ? $pdo->query("SELECT image_path FROM gallery ORDER BY RAND() LIMIT 5")->fetchAll() : [];
?>
<?php require_once 'includes/header.php'; ?>

<!-- ── Hero ── -->
<section class="hero">

  <!-- Background slideshow -->
  <?php if (!empty($heroSlides)): ?>
  <div class="hero-slides">
    <?php foreach ($heroSlides as $i => $slide): ?>
    <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>"
         style="background-image:url('<?= sanitize(UPLOADS_URL . 'gallery/' . $slide['image_path']) ?>')">
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Dark gradient overlay -->
  <div class="hero-overlay"></div>

  <!-- Slide indicator dots (only shown when slides exist) -->
  <?php if (count($heroSlides) > 1): ?>
  <div class="hero-dots" id="heroDots">
    <?php foreach ($heroSlides as $i => $slide): ?>
    <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="container" style="position:relative;z-index:2;">
    <div class="hero-content">
      <div class="hero-badge">CBC Accredited · Athi River, Kenya</div>
      <h1>Nurturing <span>Excellence</span>,<br>Growing Futures</h1>
      <p>A private primary school where every child is empowered to discover their potential through quality CBC education, strong values, and a caring community.</p>
      <div class="hero-actions">
        <a href="/apply" class="btn-primary">Apply for Admission</a>
        <a href="/about" class="btn-outline">Discover Our School</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><div class="num">500+</div><div class="lbl">Students</div></div>
        <div class="hero-stat"><div class="num">30+</div><div class="lbl">Teachers</div></div>
        <div class="hero-stat"><div class="num">15+</div><div class="lbl">Years</div></div>
        <div class="hero-stat"><div class="num">CBC</div><div class="lbl">Curriculum</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ── Why Choose Us ── -->
<section class="features">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Why Rapha Garden</span>
      <h2 class="section-title">A Place Where Children Thrive</h2>
      <p class="section-sub">We combine academic excellence with character development to raise well-rounded future leaders.</p>
    </div>
    <div class="grid-4">
      <div class="feature-card card">
        <div class="feature-icon">📚</div>
        <h3>CBC Curriculum</h3>
        <p>Fully aligned to Kenya's CBC framework from PP1 through Junior Secondary.</p>
      </div>
      <div class="feature-card card">
        <div class="feature-icon">🏆</div>
        <h3>Academic Excellence</h3>
        <p>Consistently strong KCPE results and a culture of high expectations.</p>
      </div>
      <div class="feature-card card">
        <div class="feature-icon">⚽</div>
        <h3>Co-Curriculars</h3>
        <p>Sports, music, drama, clubs and competitions to nurture every talent.</p>
      </div>
      <div class="feature-card card">
        <div class="feature-icon">🌱</div>
        <h3>Safe Environment</h3>
        <p>A secure, nurturing campus where every child feels valued and safe.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── About Snippet ── -->
<section style="background:#fff;">
  <div class="container">
    <div class="grid-2" style="align-items:center; gap:4rem;">
      <div>
        <span class="section-label">About Us</span>
        <h2 class="section-title" style="text-align:left; margin-top:0.5rem;">Rooted in Values,<br>Built for Tomorrow</h2>
        <p style="color:#555; margin-bottom:1.25rem;">Rapha Garden School was founded on the belief that education transforms lives. Located in Athi River, Machakos County, we serve families who want more for their children — academically, socially and spiritually.</p>
        <p style="color:#555; margin-bottom:2rem;">Our experienced teachers follow the CBC curriculum and employ modern, learner-centred approaches that make learning engaging, practical and meaningful.</p>
        <a href="/about" class="btn-green">Learn More About Us</a>
      </div>
      <div style="background:var(--green-50); border-radius:16px; padding:3rem 2rem; text-align:center;">
        <div style="font-size:4rem; margin-bottom:1rem;">🌿</div>
        <h3 style="color:var(--green-900); margin-bottom:0.5rem;">Our Vision</h3>
        <p style="color:#555; font-style:italic;">"To be a centre of excellence that produces holistic, God-fearing, innovative and responsible citizens."</p>
        <hr style="margin:1.5rem 0; border-color:var(--green-100);">
        <h3 style="color:var(--green-900); margin-bottom:0.5rem;">Our Mission</h3>
        <p style="color:#555; font-style:italic;">"To provide quality, affordable and holistic education through competent staff and a supportive learning environment."</p>
      </div>
    </div>
  </div>
</section>

<!-- ── Latest News ── -->
<?php if (!empty($news)): ?>
<section style="background:var(--gray-100);">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Stay Updated</span>
      <h2 class="section-title">Latest News &amp; Events</h2>
    </div>
    <div class="grid-3">
      <?php foreach ($news as $item): ?>
      <article class="card news-card">
        <?php if ($item['image']): ?>
          <img src="<?= sanitize(UPLOADS_URL . $item['image']) ?>" alt="<?= sanitize($item['title']) ?>" class="news-img">
        <?php else: ?>
          <div style="height:200px;background:var(--green-100);display:flex;align-items:center;justify-content:center;font-size:3rem;">📰</div>
        <?php endif; ?>
        <div class="card-body">
          <span class="cat-badge cat-<?= sanitize($item['category']) ?>"><?= ucfirst(sanitize($item['category'])) ?></span>
          <p class="news-date"><?= date('d M Y', strtotime($item['created_at'])) ?></p>
          <h3><?= sanitize($item['title']) ?></h3>
          <p><?= sanitize(substr($item['excerpt'] ?? $item['content'], 0, 100)) ?>...</p>
          <a href="/news?id=<?= $item['id'] ?>" class="read-more">Read More →</a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin-top:2.5rem;">
      <a href="/news" class="btn-green">View All News</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── Gallery Preview ── -->
<?php if (!empty($gallery)): ?>
<section style="background:#fff;">
  <div class="container">
    <div class="section-header">
      <span class="section-label">School Life</span>
      <h2 class="section-title">Life at Rapha Garden</h2>
      <p class="section-sub">A glimpse into our vibrant school community.</p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($gallery as $img): ?>
      <div class="gallery-item">
        <img src="<?= sanitize(UPLOADS_URL . 'gallery/' . $img['image_path']) ?>" alt="<?= sanitize($img['title'] ?? 'School photo') ?>" loading="lazy">
      </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin-top:2.5rem;">
      <a href="/gallery" class="btn-green">View Full Gallery</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── CTA Band ── -->
<div class="cta-band">
  <div class="container">
    <h2>Ready to Join the Rapha Family?</h2>
    <p>Admissions are open. Give your child the best start in life.</p>
    <div class="cta-actions">
      <a href="/admissions" class="btn-primary">Apply Now</a>
      <a href="/contact" class="btn-outline">Contact Us</a>
    </div>
  </div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
  <button class="lightbox-close" id="lightboxClose">&times;</button>
  <img id="lightboxImg" src="" alt="Gallery photo">
</div>

<?php require_once 'includes/footer.php'; ?>
