<?php
require_once 'includes/config.php';
$pageTitle = "Fee Structure";
$pageDesc  = "Rapha Garden School fee structure for all grades. Transparent and affordable quality education in Athi River, Kenya.";

$year = date('Y');
$fees = $pdo->query("SELECT * FROM fees WHERE year = '$year' ORDER BY FIELD(grade_level,'PP1','PP2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9'), FIELD(term,'Term 1','Term 2','Term 3')")->fetchAll();

// Group by grade level
$feesByGrade = [];
foreach ($fees as $fee) {
    $feesByGrade[$fee['grade_level']][] = $fee;
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Fees</div>
    <h1>Fee Structure <?= $year ?></h1>
    <p>Transparent, affordable and all-inclusive fees for every level</p>
  </div>
</div>

<section style="background:#fff;">
  <div class="container">

    <!-- Notice -->
    <div style="background:var(--gold-100); border-left:4px solid var(--gold-500); padding:1.25rem 1.5rem; border-radius:8px; margin-bottom:3rem;">
      <strong>📢 Payment Notice:</strong> Fees are payable at the beginning of each term. Early payment discounts may apply — contact the school office for details. Payments via M-Pesa and bank transfer are accepted.
    </div>

    <?php if (!empty($feesByGrade)): ?>
      <?php foreach ($feesByGrade as $grade => $terms): ?>
      <h3 style="color:var(--green-900); margin-bottom:1rem; font-size:1.15rem;"><?= sanitize($grade) ?></h3>
      <div style="overflow-x:auto; margin-bottom:2.5rem;">
        <table class="fee-table">
          <thead>
            <tr>
              <th>Term</th>
              <th>Tuition Fees</th>
              <th>Other Levies</th>
              <th>Total (KES)</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($terms as $t): ?>
            <tr>
              <td><?= sanitize($t['term']) ?></td>
              <td>KES <?= number_format($t['tuition_amount']) ?></td>
              <td>KES <?= number_format($t['levies_amount'] ?? 0) ?></td>
              <td><strong>KES <?= number_format($t['total_amount']) ?></strong></td>
              <td style="color:var(--gray-600); font-size:0.85rem;"><?= sanitize($t['notes'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endforeach; ?>

    <?php else: ?>
      <!-- Placeholder table when no DB data -->
      <div class="section-header">
        <span class="section-label">2025 / 2026</span>
        <h2 class="section-title">Current Fee Structure</h2>
        <p class="section-sub">Contact the school office or check back soon for the updated fee schedule.</p>
      </div>

      <div style="overflow-x:auto; margin-bottom:2.5rem;">
        <table class="fee-table">
          <thead>
            <tr><th>Class / Grade</th><th>Term 1 (KES)</th><th>Term 2 (KES)</th><th>Term 3 (KES)</th><th>Annual Total</th></tr>
          </thead>
          <tbody>
            <?php
            $placeholder = [
              ['PP1 / PP2',                 '12,000', '12,000', '12,000', '36,000'],
              ['Grade 1 – Grade 3',         '14,000', '14,000', '14,000', '42,000'],
              ['Grade 4 – Grade 6',         '16,000', '16,000', '16,000', '48,000'],
              ['Grade 7 – Grade 9 (JSS)',   '18,000', '18,000', '18,000', '54,000'],
            ];
            foreach ($placeholder as $row):
            ?>
            <tr>
              <td><?= $row[0] ?></td>
              <td><?= $row[1] ?></td>
              <td><?= $row[2] ?></td>
              <td><?= $row[3] ?></td>
              <td><strong><?= $row[4] ?></strong></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p style="color:var(--gray-600); font-size:0.88rem; text-align:center; margin-bottom:3rem;">* Fees are indicative. Log in as admin to update the official fee structure.</p>
    <?php endif; ?>

    <!-- What's Included -->
    <div style="background:var(--green-50); border-radius:16px; padding:3rem;">
      <h3 style="color:var(--green-900); text-align:center; margin-bottom:2rem;">What's Included in School Fees</h3>
      <div class="grid-3">
        <?php
        $includes = [
          ['📖', 'Tuition & CBC Textbooks', 'All core subject textbooks and learning materials'],
          ['🍱', 'School Lunch',            'Daily nutritious hot lunch prepared on campus'],
          ['🎒', 'Stationery Pack',         'Exercise books and basic stationery per term'],
          ['🏃', 'Sports Activities',       'PE classes and inter-school sports competitions'],
          ['🎭', 'Co-Curricular',           'Music, drama, clubs and school trips'],
          ['💻', 'ICT Access',              'Computer lab access and digital learning tools'],
        ];
        foreach ($includes as $inc):
        ?>
        <div style="display:flex; gap:1rem; align-items:flex-start;">
          <div style="font-size:1.8rem; flex-shrink:0;"><?= $inc[0] ?></div>
          <div>
            <strong style="color:var(--green-900); display:block; margin-bottom:0.25rem;"><?= $inc[1] ?></strong>
            <span style="color:#666; font-size:0.88rem;"><?= $inc[2] ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Payment Info -->
    <div style="margin-top:3rem; display:grid; grid-template-columns:1fr 1fr; gap:2rem;">
      <div class="card" style="padding:2rem;">
        <h3 style="color:var(--green-900); margin-bottom:1rem;">💳 Payment Methods</h3>
        <ul style="list-style:none; color:#555; font-size:0.92rem;">
          <li style="padding:0.5rem 0; border-bottom:1px solid var(--gray-200);">✅ M-Pesa Paybill</li>
          <li style="padding:0.5rem 0; border-bottom:1px solid var(--gray-200);">✅ Bank Transfer (Equity / Co-op Bank)</li>
          <li style="padding:0.5rem 0; border-bottom:1px solid var(--gray-200);">✅ Cash at School Office</li>
          <li style="padding:0.5rem 0;">✅ Cheque (payable to Rapha Garden School)</li>
        </ul>
      </div>
      <div class="card" style="padding:2rem;">
        <h3 style="color:var(--green-900); margin-bottom:1rem;">📅 Payment Deadlines</h3>
        <ul style="list-style:none; color:#555; font-size:0.92rem;">
          <li style="padding:0.5rem 0; border-bottom:1px solid var(--gray-200);">📌 Term 1 — January (before reporting day)</li>
          <li style="padding:0.5rem 0; border-bottom:1px solid var(--gray-200);">📌 Term 2 — May (before reporting day)</li>
          <li style="padding:0.5rem 0; border-bottom:1px solid var(--gray-200);">📌 Term 3 — September (before reporting day)</li>
          <li style="padding:0.5rem 0;">📞 Instalment plans available — call us</li>
        </ul>
      </div>
    </div>

  </div>
</section>

<div class="cta-band">
  <div class="container">
    <h2>Have Questions About Fees?</h2>
    <p>Our admin office is happy to help with payment plans and queries.</p>
    <div class="cta-actions">
      <a href="/contact" class="btn-primary">Contact Admin Office</a>
      <a href="/admissions" class="btn-outline">Start Application</a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
