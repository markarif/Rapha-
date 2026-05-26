<?php
require_once 'includes/config.php';
$pageTitle = "Contact Us";
$pageDesc  = "Get in touch with Rapha Garden School in Athi River, Machakos County, Kenya.";

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $errors = [];
    if (!$name)    $errors[] = 'Name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (!$message) $errors[] = 'Message is required.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        $flash = ['type' => 'success', 'message' => '✅ Thank you, ' . htmlspecialchars($name) . '! Your message has been received. We will get back to you shortly.'];
    } else {
        $flash = ['type' => 'error', 'message' => implode(' ', $errors)];
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> / Contact</div>
    <h1>Contact Us</h1>
    <p>We'd love to hear from you — reach out anytime</p>
  </div>
</div>

<section style="background:#fff;">
  <div class="container">
    <div class="grid-2" style="gap:4rem; align-items:flex-start;">

      <!-- Contact Info -->
      <div>
        <h2 style="color:var(--green-900); margin-bottom:2rem; font-size:1.6rem;">Get in Touch</h2>

        <div style="display:flex; flex-direction:column; gap:1.5rem; margin-bottom:2.5rem;">
          <?php
          $contacts = [
            ['📍', 'Our Location',  SITE_ADDRESS,  null],
            ['📞', 'Phone',         SITE_PHONE,     'tel:' . SITE_PHONE],
            ['✉️', 'Email',         SITE_EMAIL,     'mailto:' . SITE_EMAIL],
            ['🕐', 'Office Hours',  'Mon–Fri: 7:00 AM – 5:00 PM<br>Saturday: 8:00 AM – 12:00 PM', null],
          ];
          foreach ($contacts as $c):
          ?>
          <div style="display:flex; gap:1.25rem; align-items:flex-start;">
            <div style="width:48px;height:48px;background:var(--green-50);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;"><?= $c[0] ?></div>
            <div>
              <div style="font-weight:600; color:var(--green-900); margin-bottom:0.25rem;"><?= $c[1] ?></div>
              <?php if ($c[3]): ?>
                <a href="<?= $c[3] ?>" style="color:#555; font-size:0.92rem;"><?= $c[2] ?></a>
              <?php else: ?>
                <div style="color:#555; font-size:0.92rem;"><?= $c[2] ?></div>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Map placeholder -->
        <div style="background:var(--green-50); border-radius:12px; height:280px; display:flex; align-items:center; justify-content:center; flex-direction:column; color:var(--gray-600);">
          <div style="font-size:3rem; margin-bottom:1rem;">🗺️</div>
          <strong style="color:var(--green-900);">Athi River, Machakos County</strong>
          <p style="font-size:0.85rem; margin-top:0.5rem;">
            <a href="https://maps.google.com/?q=Athi+River+Machakos+Kenya" target="_blank" rel="noopener" style="color:var(--green-700); font-weight:600;">Open in Google Maps →</a>
          </p>
          <p style="font-size:0.78rem; margin-top:0.5rem; color:var(--gray-600);">(Embed your Google Maps iframe here)</p>
        </div>
      </div>

      <!-- Contact Form -->
      <div>
        <h2 style="color:var(--green-900); margin-bottom:2rem; font-size:1.6rem;">Send us a Message</h2>

        <?php if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>

        <form method="POST" action="/contact">
          <div class="grid-2" style="gap:1rem;">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" required placeholder="Jane Wanjiku" value="<?= sanitize($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" placeholder="+254 7XX XXX XXX" value="<?= sanitize($_POST['phone'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required placeholder="jane@example.com" value="<?= sanitize($_POST['email'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <select id="subject" name="subject">
              <option value="">Select a topic...</option>
              <option value="Admissions Enquiry">Admissions Enquiry</option>
              <option value="Fee Structure">Fee Structure</option>
              <option value="School Visit">Book a School Visit</option>
              <option value="General Enquiry">General Enquiry</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="form-group">
            <label for="message">Your Message *</label>
            <textarea id="message" name="message" required placeholder="Write your message here..."><?= sanitize($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn-primary" style="width:100%;">Send Message 📨</button>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- WhatsApp CTA -->
<div style="background:var(--green-50); padding:3rem 0; text-align:center;">
  <div class="container">
    <h3 style="color:var(--green-900); margin-bottom:0.75rem;">Prefer WhatsApp?</h3>
    <p style="color:#555; margin-bottom:1.5rem;">Chat with us directly on WhatsApp for quick responses.</p>
    <a href="https://wa.me/254722272063?text=Hello%20Rapha%20Garden%20School%2C%20I%20would%20like%20to%20enquire%20about..." target="_blank" rel="noopener"
       style="display:inline-flex;align-items:center;gap:0.75rem;background:#25D366;color:#fff;font-weight:700;padding:0.9rem 2rem;border-radius:10px;font-size:1rem;transition:background 0.2s;"
       onmouseover="this.style.background='#1da851'" onmouseout="this.style.background='#25D366'">
      💬 Chat on WhatsApp
    </a>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
