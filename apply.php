<?php
require_once 'includes/config.php';
$pageTitle = "Apply for Admission";
$pageDesc  = "Apply online for admission to Rapha Garden School, Athi River.";

$flash  = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name    = trim($_POST['student_name']    ?? '');
    $date_of_birth   = trim($_POST['date_of_birth']   ?? '');
    $grade_applying  = trim($_POST['grade_applying']  ?? '');
    $parent_name     = trim($_POST['parent_name']     ?? '');
    $parent_phone    = trim($_POST['parent_phone']    ?? '');
    $parent_email    = trim($_POST['parent_email']    ?? '');
    $address         = trim($_POST['address']         ?? '');
    $previous_school = trim($_POST['previous_school'] ?? '');
    $additional_info = trim($_POST['additional_info'] ?? '');

    if (!$student_name)  $errors[] = 'Student\'s full name is required.';
    if (!$grade_applying) $errors[] = 'Please select the grade applying for.';
    if (!$parent_name)   $errors[] = 'Parent/guardian name is required.';
    if (!$parent_phone)  $errors[] = 'Parent/guardian phone number is required.';
    if ($parent_email && !filter_var($parent_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

    if (!$errors && $pdo) {
        $stmt = $pdo->prepare("INSERT INTO applications
            (student_name,date_of_birth,grade_applying,parent_name,parent_phone,parent_email,address,previous_school,additional_info,status,created_at)
            VALUES (?,?,?,?,?,?,?,?,?,'new',NOW())");
        $stmt->execute([
            $student_name, $date_of_birth ?: null, $grade_applying,
            $parent_name, $parent_phone, $parent_email,
            $address, $previous_school, $additional_info
        ]);
        $flash = ['type'=>'success', 'message'=>'✅ Thank you! Your application has been received. We will contact you shortly to confirm next steps.'];
        // Clear form
        $_POST = [];
    } elseif ($errors) {
        $flash = ['type'=>'error', 'message'=>implode('<br>', $errors)];
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / <a href="/admissions">Admissions</a> / Apply</div>
    <h1>Online Application Form</h1>
    <p>Fill in the form below and we will get back to you within 2 working days</p>
  </div>
</div>

<section style="background:#fff;">
  <div class="container" style="max-width:800px;">

    <?php if (!empty($flash) && $flash['type']==='success'): ?>
    <div style="text-align:center;padding:3rem 2rem;background:var(--green-50);border-radius:16px;border:1px solid var(--green-100);">
      <div style="font-size:4rem;margin-bottom:1rem;">✅</div>
      <h2 style="color:var(--green-900);margin-bottom:1rem;">Application Submitted!</h2>
      <p style="color:#555;font-size:1.05rem;margin-bottom:2rem;"><?= $flash['message'] ?></p>
      <a href="/" class="btn-green">Back to Home</a>
    </div>

    <?php else: ?>

    <div style="background:var(--gold-100);border-left:4px solid var(--gold-500);padding:1rem 1.25rem;border-radius:8px;margin-bottom:2rem;">
      <strong>📋 Before you apply:</strong> Please read our <a href="/admissions" style="color:var(--green-700);font-weight:600;">admissions requirements</a> and ensure you have all required documents ready for when we call you.
    </div>

    <?php if (!empty($flash) && $flash['type']==='error'): ?>
    <div class="flash flash-error"><?= $flash['message'] ?></div>
    <?php endif; ?>

    <form method="POST" action="/apply">
      <!-- Student Details -->
      <div style="background:var(--green-50);border-radius:12px;padding:1.75rem;margin-bottom:1.75rem;">
        <h3 style="color:var(--green-900);margin-bottom:1.25rem;font-size:1.05rem;">👦 Student Details</h3>
        <div class="grid-2" style="gap:1rem;">
          <div class="form-group" style="margin:0;">
            <label>Student Full Name *</label>
            <input type="text" name="student_name" required placeholder="As per birth certificate" value="<?= sanitize($_POST['student_name'] ?? '') ?>">
          </div>
          <div class="form-group" style="margin:0;">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" value="<?= sanitize($_POST['date_of_birth'] ?? '') ?>">
          </div>
          <div class="form-group" style="margin:0; grid-column:span 2;">
            <label>Grade / Level Applying For *</label>
            <select name="grade_applying" required>
              <option value="">-- Select Grade --</option>
              <?php
              $grades = ['PP1','PP2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9'];
              foreach ($grades as $g):
              ?>
              <option value="<?= $g ?>" <?= ($_POST['grade_applying'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin:0; grid-column:span 2;">
            <label>Previous School (if any)</label>
            <input type="text" name="previous_school" placeholder="Leave blank if new entrant (PP1/PP2)" value="<?= sanitize($_POST['previous_school'] ?? '') ?>">
          </div>
        </div>
      </div>

      <!-- Parent / Guardian -->
      <div style="background:var(--green-50);border-radius:12px;padding:1.75rem;margin-bottom:1.75rem;">
        <h3 style="color:var(--green-900);margin-bottom:1.25rem;font-size:1.05rem;">👨‍👩‍👦 Parent / Guardian Details</h3>
        <div class="grid-2" style="gap:1rem;">
          <div class="form-group" style="margin:0;">
            <label>Parent / Guardian Full Name *</label>
            <input type="text" name="parent_name" required placeholder="Full name" value="<?= sanitize($_POST['parent_name'] ?? '') ?>">
          </div>
          <div class="form-group" style="margin:0;">
            <label>Phone Number *</label>
            <input type="tel" name="parent_phone" required placeholder="+254 7XX XXX XXX" value="<?= sanitize($_POST['parent_phone'] ?? '') ?>">
          </div>
          <div class="form-group" style="margin:0;">
            <label>Email Address (optional)</label>
            <input type="email" name="parent_email" placeholder="yourname@example.com" value="<?= sanitize($_POST['parent_email'] ?? '') ?>">
          </div>
          <div class="form-group" style="margin:0;">
            <label>Home Address / Estate</label>
            <input type="text" name="address" placeholder="e.g. Athi River, Mlolongo" value="<?= sanitize($_POST['address'] ?? '') ?>">
          </div>
        </div>
      </div>

      <!-- Additional Info -->
      <div class="form-group">
        <label>Additional Information (optional)</label>
        <textarea name="additional_info" placeholder="Any special needs, questions, or information we should know..."><?= sanitize($_POST['additional_info'] ?? '') ?></textarea>
      </div>

      <p style="color:var(--gray-600);font-size:0.85rem;margin-bottom:1.5rem;">
        By submitting this form you agree that the information provided is accurate. Submission does not guarantee admission — our team will contact you to confirm next steps.
      </p>

      <button type="submit" class="btn-primary" style="width:100%;font-size:1.05rem;padding:1rem;">
        Submit Application 🚀
      </button>
    </form>

    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
