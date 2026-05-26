<?php
require_once '../includes/config.php';
requireAdmin();

$flash = flashGet();
$action = $_GET['action'] ?? 'list';
$editId = intval($_GET['edit'] ?? 0);

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM fees WHERE id=?")->execute([intval($_GET['delete'])]);
    flashSet('success', 'Fee record deleted.');
    redirect('/admin/fees.php');
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = intval($_POST['id'] ?? 0);
    $grade_level    = trim($_POST['grade_level']    ?? '');
    $term           = trim($_POST['term']           ?? '');
    $tuition_amount = intval($_POST['tuition_amount'] ?? 0);
    $levies_amount  = intval($_POST['levies_amount']  ?? 0);
    $total_amount   = intval($_POST['total_amount']   ?? 0);
    $year           = intval($_POST['year']           ?? date('Y'));
    $notes          = trim($_POST['notes']            ?? '');

    if (!$grade_level || !$term || !$tuition_amount) {
        flashSet('error', 'Grade, Term and Tuition amount are required.');
        redirect('/admin/fees.php?action=new' . ($id ? '&edit='.$id : ''));
    }

    if ($total_amount === 0) $total_amount = $tuition_amount + $levies_amount;

    if ($id) {
        $pdo->prepare("UPDATE fees SET grade_level=?,term=?,tuition_amount=?,levies_amount=?,total_amount=?,year=?,notes=? WHERE id=?")
            ->execute([$grade_level,$term,$tuition_amount,$levies_amount,$total_amount,$year,$notes,$id]);
        flashSet('success','Fee record updated.');
    } else {
        $pdo->prepare("INSERT INTO fees (grade_level,term,tuition_amount,levies_amount,total_amount,year,notes,created_at) VALUES (?,?,?,?,?,?,?,NOW())")
            ->execute([$grade_level,$term,$tuition_amount,$levies_amount,$total_amount,$year,$notes]);
        flashSet('success','Fee record added.');
    }
    redirect('/admin/fees.php');
}

// Load for edit
$editRow = null;
if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM fees WHERE id=?");
    $stmt->execute([$editId]);
    $editRow = $stmt->fetch();
    $action = 'new';
}

$fees = $pdo->query("SELECT * FROM fees ORDER BY year DESC, FIELD(grade_level,'PP1','PP2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9'), FIELD(term,'Term 1','Term 2','Term 3')")->fetchAll();
$flash = $flash ?? flashGet();

$grades = ['PP1','PP2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9'];
$terms  = ['Term 1','Term 2','Term 3'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Fees | Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <h1 style="font-size:1.25rem;color:var(--green-900);">💰 Fee Structure</h1>
    <?php if ($action !== 'new'): ?>
    <a href="?action=new" class="btn-primary btn-sm">+ Add Fee Record</a>
    <?php else: ?>
    <a href="/admin/fees.php" style="color:var(--gray-600);font-size:0.88rem;">← Back to list</a>
    <?php endif; ?>
  </div>

  <?php if ($flash): ?>
  <div class="flash flash-<?= $flash['type'] ?>"><?= sanitize($flash['message']) ?></div>
  <?php endif; ?>

  <?php if ($action === 'new'): ?>
  <div class="admin-card">
    <h2><?= $editRow ? 'Edit Fee Record' : 'Add Fee Record' ?></h2>
    <form method="POST">
      <?php if ($editRow): ?><input type="hidden" name="id" value="<?= $editRow['id'] ?>"><?php endif; ?>
      <div class="grid-2" style="gap:1rem;">
        <div class="form-group">
          <label>Grade / Level *</label>
          <select name="grade_level" required>
            <?php foreach ($grades as $g): ?>
            <option value="<?= $g ?>" <?= ($editRow['grade_level'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Term *</label>
          <select name="term" required>
            <?php foreach ($terms as $t): ?>
            <option value="<?= $t ?>" <?= ($editRow['term'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Year</label>
          <input type="number" name="year" value="<?= $editRow['year'] ?? date('Y') ?>" min="2020" max="2030">
        </div>
        <div class="form-group">
          <label>Tuition Amount (KES) *</label>
          <input type="number" name="tuition_amount" required min="0" value="<?= $editRow['tuition_amount'] ?? '' ?>" placeholder="e.g. 15000">
        </div>
        <div class="form-group">
          <label>Other Levies (KES)</label>
          <input type="number" name="levies_amount" min="0" value="<?= $editRow['levies_amount'] ?? 0 ?>" placeholder="0">
        </div>
        <div class="form-group">
          <label>Total Amount (KES) <small style="color:var(--gray-600);">(auto-calculated if blank)</small></label>
          <input type="number" name="total_amount" min="0" value="<?= $editRow['total_amount'] ?? '' ?>" placeholder="auto">
        </div>
      </div>
      <div class="form-group">
        <label>Notes / Remarks</label>
        <input type="text" name="notes" placeholder="e.g. Includes lunch, uniform deposit" value="<?= sanitize($editRow['notes'] ?? '') ?>">
      </div>
      <div style="display:flex;gap:1rem;">
        <button type="submit" class="btn-primary"><?= $editRow ? '💾 Update' : '➕ Add Record' ?></button>
        <a href="/admin/fees.php" class="btn-green">Cancel</a>
      </div>
    </form>
  </div>

  <?php else: ?>
  <div class="admin-card">
    <h2>All Fee Records (<?= count($fees) ?>)</h2>
    <?php if ($fees): ?>
    <div style="overflow-x:auto;">
    <table class="admin-table">
      <thead><tr><th>Grade</th><th>Term</th><th>Year</th><th>Tuition</th><th>Levies</th><th>Total</th><th>Notes</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($fees as $f): ?>
        <tr>
          <td style="font-weight:600;"><?= sanitize($f['grade_level']) ?></td>
          <td><?= sanitize($f['term']) ?></td>
          <td><?= $f['year'] ?></td>
          <td>KES <?= number_format($f['tuition_amount']) ?></td>
          <td>KES <?= number_format($f['levies_amount'] ?? 0) ?></td>
          <td style="font-weight:700;color:var(--green-900);">KES <?= number_format($f['total_amount']) ?></td>
          <td style="color:var(--gray-600);font-size:0.82rem;"><?= sanitize($f['notes'] ?? '') ?></td>
          <td>
            <a href="?action=new&edit=<?= $f['id'] ?>" style="color:var(--green-700);font-weight:600;font-size:0.85rem;margin-right:0.75rem;">Edit</a>
            <a href="?delete=<?= $f['id'] ?>" onclick="return confirm('Delete this record?')" style="color:#c62828;font-weight:600;font-size:0.85rem;">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php else: ?>
    <p style="color:var(--gray-600);">No fee records yet. Click "Add Fee Record" to get started.</p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
