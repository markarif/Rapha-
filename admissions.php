<?php
require_once 'includes/config.php';
$pageTitle = "Admissions";
$pageDesc  = "Apply to Rapha Garden School. Learn about our admission process, requirements and how to enroll your child.";
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Admissions</div>
    <h1>Admissions</h1>
    <p>We welcome new students for all levels — PP1 through Junior Secondary</p>
  </div>
</div>

<section style="background:#fff;">
  <div class="container">
    <div style="text-align:center; background:var(--gold-100); border-radius:12px; padding:1.5rem 2rem; margin-bottom:4rem; border:1px solid var(--gold-500);">
      <strong style="color:var(--green-900);">🎉 Admissions are currently OPEN for the <?= date('Y') ?>/<?= date('Y')+1 ?> academic year.</strong>
      &nbsp; <a href="/apply" class="btn-primary btn-sm" style="margin-left:1rem;">Apply Now</a>
    </div>

    <!-- How to Apply -->
    <div id="how-to-apply" style="padding-top:1rem;">
      <div class="section-header">
        <span class="section-label">Step by Step</span>
        <h2 class="section-title">How to Apply</h2>
      </div>
      <div style="max-width:800px; margin:0 auto;">
        <?php
        $steps = [
          ['01', 'Visit the School',         'Come to our campus at Athi River for a guided tour and to meet our team. You can also call us to schedule a visit.'],
          ['02', 'Collect Application Form', 'Pick up the admission form from the school office or download it from this page. Fill it in completely.'],
          ['03', 'Submit Documents',         'Return the completed form with all required documents to the admissions office.'],
          ['04', 'Assessment',               'Where applicable, the child may sit a brief placement assessment to determine the right class level.'],
          ['05', 'Receive Offer Letter',      'Successful applicants receive an admission offer letter. Confirm acceptance by paying the admission fee.'],
          ['06', 'Reporting Day',             'Arrive on the designated reporting day with all required items. Welcome to the Rapha Garden family!'],
        ];
        foreach ($steps as $step):
        ?>
        <div style="display:flex; gap:1.5rem; margin-bottom:2rem; align-items:flex-start;">
          <div style="width:52px;height:52px;background:var(--green-900);color:var(--gold-500);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-family:'Merriweather',serif;font-size:1rem;flex-shrink:0;"><?= $step[0] ?></div>
          <div>
            <h3 style="color:var(--green-900); margin-bottom:0.4rem; font-size:1.05rem;"><?= $step[1] ?></h3>
            <p style="color:#555; font-size:0.92rem;"><?= $step[2] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- Requirements -->
<section id="requirements" style="background:var(--gray-100);">
  <div class="container">
    <div class="section-header">
      <span class="section-label">What You Need</span>
      <h2 class="section-title">Admission Requirements</h2>
    </div>
    <div class="grid-2" style="gap:3rem;">
      <div class="card" style="padding:2rem;">
        <h3 style="color:var(--green-900); margin-bottom:1.25rem;">📋 Documents Required</h3>
        <ul style="list-style:none; color:#555; font-size:0.92rem;">
          <?php
          $docs = [
            'Completed admission application form',
            'Copy of birth certificate',
            'Passport photo (2 copies)',
            'Previous school leaving/transfer certificate (Gr 2+)',
            'Previous school report (last 2 terms)',
            'Copy of parent/guardian National ID',
            'Immunisation card (for new entrants)',
          ];
          foreach ($docs as $doc):
          ?>
          <li style="padding:0.6rem 0; border-bottom:1px solid var(--gray-200); display:flex; gap:0.75rem;">
            <span style="color:var(--green-600);">✓</span> <?= $doc ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <div class="card" style="padding:2rem; margin-bottom:1.5rem;">
          <h3 style="color:var(--green-900); margin-bottom:1rem;">🎒 Age Requirements</h3>
          <table style="width:100%; font-size:0.9rem;">
            <tr style="background:var(--green-50);"><th style="padding:0.6rem;text-align:left;">Level</th><th style="padding:0.6rem;text-align:left;">Age</th></tr>
            <tr><td style="padding:0.6rem;">PP1</td><td style="padding:0.6rem;">4 years</td></tr>
            <tr style="background:var(--green-50);"><td style="padding:0.6rem;">PP2</td><td style="padding:0.6rem;">5 years</td></tr>
            <tr><td style="padding:0.6rem;">Grade 1</td><td style="padding:0.6rem;">6 years</td></tr>
            <tr style="background:var(--green-50);"><td style="padding:0.6rem;">Grade 7 (JSS)</td><td style="padding:0.6rem;">12–13 years</td></tr>
          </table>
        </div>
        <div class="card" style="padding:2rem;">
          <h3 style="color:var(--green-900); margin-bottom:0.75rem;">💰 Admission Fee</h3>
          <p style="color:#555; font-size:0.92rem;">A one-time non-refundable admission fee is payable upon acceptance. Contact the office for the current amount.</p>
          <a href="/contact" style="color:var(--green-700); font-weight:600; font-size:0.9rem; display:inline-block; margin-top:0.75rem;">→ Contact Admin Office</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Download Forms -->
<section id="forms" style="background:#fff;">
  <div class="container">
    <div class="section-header">
      <span class="section-label">Get Started</span>
      <h2 class="section-title">Download Admission Forms</h2>
      <p class="section-sub">Print, fill and bring to the school office or submit via email.</p>
    </div>
    <?php
    $forms = $pdo ? $pdo->query("SELECT * FROM forms ORDER BY created_at DESC")->fetchAll() : [];
    ?>
    <?php if (!empty($forms)): ?>
    <div class="grid-3">
      <?php foreach ($forms as $form): ?>
      <div class="card" style="padding:2rem; text-align:center;">
        <div style="font-size:3rem; margin-bottom:1rem;">📄</div>
        <h3 style="color:var(--green-900); margin-bottom:0.25rem; font-size:1rem;"><?= sanitize($form['title']) ?></h3>
        <p style="color:var(--gray-600); font-size:0.85rem; margin-bottom:0.5rem;"><?= sanitize($form['level'] ?: '') ?></p>
        <?php if ($form['description']): ?>
        <p style="color:var(--gray-600); font-size:0.82rem; margin-bottom:1rem;"><?= sanitize($form['description']) ?></p>
        <?php endif; ?>
        <a href="<?= UPLOADS_URL ?>forms/<?= sanitize($form['filename']) ?>" class="btn-green btn-sm" download>⬇ Download</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:2.5rem;background:var(--green-50);border-radius:12px;">
      <p style="color:var(--gray-600);">📄 Forms are being uploaded. Please visit the school office or <a href="/contact" style="color:var(--green-700);font-weight:600;">contact us</a> to request a form.</p>
    </div>
    <?php endif; ?>
    <p style="text-align:center; color:var(--gray-600); margin-top:1.5rem; font-size:0.88rem;">Forms can also be collected from the school administration office in person.</p>
  </div>
</section>

<!-- FAQ -->
<section style="background:var(--gray-100);">
  <div class="container" style="max-width:800px;">
    <div class="section-header">
      <span class="section-label">FAQs</span>
      <h2 class="section-title">Common Questions</h2>
    </div>
    <?php
    $faqs = [
      ['Do you have boarding facilities?',                 'No, Rapha Garden School is a day school. We operate from 7:00 AM to 4:00 PM Monday to Friday.'],
      ['What language is used for instruction?',           'English is the primary language of instruction, with Kiswahili taught as a subject per CBC requirements.'],
      ['Do you accept mid-term transfers?',                'Yes, we accept transfer students throughout the year subject to available space. Contact the office to check availability.'],
      ['Is there a school bus service?',                   'Yes, we have a school bus route covering select areas around Athi River. Ask the office for the current routes and costs.'],
      ['What happens if I cannot afford to pay full fees?','We offer flexible payment plans. Please speak to the bursar in confidence to arrange an instalment plan that works for you.'],
    ];
    foreach ($faqs as $faq):
    ?>
    <div class="accordion-item">
      <button class="accordion-btn"><?= $faq[0] ?> <span class="accordion-icon">+</span></button>
      <div class="accordion-body"><p><?= $faq[1] ?></p></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<div class="cta-band">
  <div class="container">
    <h2>Ready to Enrol?</h2>
    <p>Visit us or call us today. We'd love to welcome you to Rapha Garden School.</p>
    <div class="cta-actions">
      <a href="/contact" class="btn-primary">Contact Us</a>
      <a href="tel:<?= SITE_PHONE ?>" class="btn-outline">📞 Call Now</a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
