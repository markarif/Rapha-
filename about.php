<?php
require_once 'includes/config.php';
$pageTitle = "About Us";
$pageDesc  = "Learn about Rapha Garden School — our story, vision, mission, and dedicated team in Athi River, Machakos County.";
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / About</div>
    <h1>About Rapha Garden School</h1>
    <p>Our story, values, and the people behind the school</p>
  </div>
</div>

<!-- ── Our Story ── -->
<section id="story" style="background:#fff;">
  <div class="container">
    <div class="grid-2" style="align-items:center; gap:4rem;">
      <div>
        <span class="section-label">Our Story</span>
        <h2 class="section-title" style="text-align:left; margin-top:0.5rem;">From Humble Beginnings<br>to Growing Excellence</h2>
        <p style="color:#555; margin-bottom:1.25rem;">Rapha Garden School was established in Athi River, Machakos County with a clear purpose — to provide quality, affordable and holistic education to children in the community. The name "Rapha" (meaning healing and restoration) reflects our belief that great education heals communities and restores hope.</p>
        <p style="color:#555; margin-bottom:1.25rem;">From our early days with a handful of classrooms, we have grown into a fully-equipped school offering learning from Pre-Primary 1 through Junior Secondary School, fully aligned to Kenya's Competency-Based Curriculum (CBC).</p>
        <p style="color:#555;">Today, we are proud to serve hundreds of families across Athi River and beyond, combining academic rigour with strong moral values and co-curricular activities that produce well-rounded graduates.</p>
      </div>
      <div style="background:var(--green-50); border-radius:16px; padding:3rem 2rem;">
        <div style="text-align:center; font-size:3.5rem; margin-bottom:1.5rem;">🏫</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; text-align:center;">
          <div>
            <div style="font-size:2rem; font-weight:900; color:var(--green-900); font-family:'Merriweather',serif;">500+</div>
            <div style="font-size:0.85rem; color:var(--gray-600);">Students Enrolled</div>
          </div>
          <div>
            <div style="font-size:2rem; font-weight:900; color:var(--green-900); font-family:'Merriweather',serif;">30+</div>
            <div style="font-size:0.85rem; color:var(--gray-600);">Qualified Teachers</div>
          </div>
          <div>
            <div style="font-size:2rem; font-weight:900; color:var(--green-900); font-family:'Merriweather',serif;">15+</div>
            <div style="font-size:0.85rem; color:var(--gray-600);">Years of Excellence</div>
          </div>
          <div>
            <div style="font-size:2rem; font-weight:900; color:var(--green-900); font-family:'Merriweather',serif;">CBC</div>
            <div style="font-size:0.85rem; color:var(--gray-600);">Accredited Curriculum</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Vision & Mission ── -->
<section id="vision" style="background:var(--green-900); color:#fff;">
  <div class="container">
    <div class="section-header">
      <span class="section-label" style="background:rgba(255,255,255,0.15); color:#fff;">Our Purpose</span>
      <h2 class="section-title" style="color:#fff;">Vision &amp; Mission</h2>
    </div>
    <div class="grid-2" style="gap:3rem;">
      <div style="background:rgba(255,255,255,0.08); border-radius:16px; padding:2.5rem; border-left:4px solid var(--gold-500);">
        <div style="font-size:2.5rem; margin-bottom:1rem;">🔭</div>
        <h3 style="color:var(--gold-500); margin-bottom:1rem; font-size:1.3rem;">Our Vision</h3>
        <p style="color:#c8e6c9; font-size:1.05rem; font-style:italic; line-height:1.8;">
          "To be a centre of excellence that produces holistic, God-fearing, innovative and responsible citizens who positively impact their communities and the nation."
        </p>
      </div>
      <div style="background:rgba(255,255,255,0.08); border-radius:16px; padding:2.5rem; border-left:4px solid var(--green-500);">
        <div style="font-size:2.5rem; margin-bottom:1rem;">🎯</div>
        <h3 style="color:var(--green-500); margin-bottom:1rem; font-size:1.3rem;">Our Mission</h3>
        <p style="color:#c8e6c9; font-size:1.05rem; font-style:italic; line-height:1.8;">
          "To provide quality, affordable and holistic education through competent staff, modern facilities and a supportive learning environment that nurtures every child's potential."
        </p>
      </div>
    </div>
    <div style="display:flex; justify-content:center; gap:3rem; flex-wrap:wrap; margin-top:3rem;">
      <?php
      $values = ['Integrity','Excellence','Respect','Innovation','Community'];
      foreach ($values as $v):
      ?>
      <div style="text-align:center; color:#c8e6c9;">
        <div style="width:56px;height:56px;background:rgba(255,255,255,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 0.5rem;">✦</div>
        <div style="font-weight:600; font-size:0.9rem;"><?= $v ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Team ── -->
<section id="team" style="background:#fff;">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Leadership</span>
      <h2 class="section-title">Our Team</h2>
      <p class="section-sub">Dedicated educators committed to your child's growth and success.</p>
    </div>
    <?php
    $team = $pdo ? $pdo->query("SELECT * FROM team ORDER BY sort_order ASC, id ASC")->fetchAll() : [];
    ?>
    <?php if (!empty($team)): ?>
    <div class="grid-3">
      <?php foreach ($team as $member): ?>
      <div class="card" style="text-align:center; padding:2rem 1.5rem;">
        <?php if ($member['photo']): ?>
          <img src="<?= sanitize(UPLOADS_URL . 'gallery/' . $member['photo']) ?>"
               alt="<?= sanitize($member['name']) ?>"
               style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 1rem;">
        <?php else: ?>
          <div style="width:80px;height:80px;background:var(--green-50);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.2rem;margin:0 auto 1rem;"><?= $member['icon'] ?: '👤' ?></div>
        <?php endif; ?>
        <h3 style="color:var(--green-900); margin-bottom:0.25rem; font-size:1.05rem;"><?= sanitize($member['name']) ?></h3>
        <div style="color:var(--gold-600); font-size:0.85rem; font-weight:600; margin-bottom:0.75rem;"><?= sanitize($member['role']) ?></div>
        <p style="color:#666; font-size:0.88rem;"><?= sanitize($member['description']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:3rem;color:var(--gray-600);">
      <div style="font-size:3rem;margin-bottom:1rem;">👥</div>
      <p>Our team profiles are being updated. Check back soon.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ── CTA ── -->
<div class="cta-band">
  <div class="container">
    <h2>Join the Rapha Garden Family</h2>
    <p>Give your child an education built on excellence and values.</p>
    <div class="cta-actions">
      <a href="/admissions" class="btn-primary">Apply Now</a>
      <a href="/contact" class="btn-outline">Get in Touch</a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
