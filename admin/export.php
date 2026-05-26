<?php
require_once '../includes/config.php';
requireAdmin();

$type   = $_GET['type']   ?? '';
$format = $_GET['format'] ?? 'csv';

if (!$pdo) die('Database connection unavailable.');

// ── Define data sets ────────────────────────────────────────────
switch ($type) {

    case 'contacts':
        $rows    = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
        $title   = 'Contact Messages';
        $headers = ['ID','Name','Email','Phone','Subject','Message','Date Received'];
        $fields  = ['id','name','email','phone','subject','message','created_at'];
        break;

    case 'applications':
        $rows    = $pdo->query("SELECT * FROM applications ORDER BY created_at DESC")->fetchAll();
        $title   = 'Admission Applications';
        $headers = ['ID','Student Name','Date of Birth','Grade','Previous School','Parent Name','Parent Phone','Parent Email','Address','Additional Info','Status','Date Submitted'];
        $fields  = ['id','student_name','date_of_birth','grade_applying','previous_school','parent_name','parent_phone','parent_email','address','additional_info','status','created_at'];
        break;

    case 'fees':
        $rows    = $pdo->query("SELECT * FROM fees ORDER BY year DESC, grade_level, term")->fetchAll();
        $title   = 'Fee Structure';
        $headers = ['ID','Grade / Level','Term','Year','Tuition (KES)','Levies (KES)','Total (KES)','Notes'];
        $fields  = ['id','grade_level','term','year','tuition_amount','levies_amount','total_amount','notes'];
        break;

    default:
        die('Invalid export type. Use: contacts, applications, or fees.');
}

$exportDate = date('d M Y');
$filename   = $type . '_' . date('Y-m-d');

// ── CSV Export ───────────────────────────────────────────────────
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    // UTF-8 BOM so Excel opens it correctly
    fputs($out, "\xEF\xBB\xBF");
    // Title row
    fputcsv($out, [SITE_NAME . ' — ' . $title]);
    fputcsv($out, ['Exported: ' . $exportDate]);
    fputcsv($out, []);
    // Column headers
    fputcsv($out, $headers);
    // Data rows
    foreach ($rows as $row) {
        $line = [];
        foreach ($fields as $f) {
            $line[] = $row[$f] ?? '';
        }
        fputcsv($out, $line);
    }
    fputcsv($out, []);
    fputcsv($out, ['Total records: ' . count($rows)]);
    fclose($out);
    exit;
}

// ── PDF Print View ───────────────────────────────────────────────
if ($format === 'pdf') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title) ?> — <?= SITE_NAME ?></title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; background: #fff; padding: 20px; }

    .print-header { border-bottom: 3px solid #1B5E20; padding-bottom: 12px; margin-bottom: 16px; display:flex; justify-content:space-between; align-items:flex-end; }
    .print-header h1 { font-size: 18px; color: #1B5E20; }
    .print-header p  { font-size: 10px; color: #666; margin-top: 3px; }
    .print-header .meta { text-align:right; font-size:10px; color:#666; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1B5E20; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
    tr:nth-child(even) td { background: #f5f9f5; }

    .badge { display:inline-block; padding:1px 7px; border-radius:10px; font-weight:700; font-size:9px; }
    .badge-new      { background:#e3f2fd; color:#1565c0; }
    .badge-reviewed { background:#fff3e0; color:#e65100; }
    .badge-accepted { background:#e8f5e9; color:#2e7d32; }
    .badge-rejected { background:#ffebee; color:#c62828; }

    .footer { margin-top:20px; font-size:9px; color:#999; border-top:1px solid #ddd; padding-top:8px; display:flex; justify-content:space-between; }

    .no-print { margin-bottom: 16px; }
    @media print {
      .no-print { display: none !important; }
      body { padding: 10px; }
      th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      tr:nth-child(even) td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
  </style>
</head>
<body>

<div class="no-print" style="display:flex;gap:10px;align-items:center;">
  <button onclick="window.print()" style="background:#1B5E20;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:700;">🖨 Print / Save as PDF</button>
  <a href="/admin/export.php?type=<?= $type ?>&format=csv" style="background:#F9A825;color:#333;text-decoration:none;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:700;">⬇ Download CSV</a>
  <a href="javascript:history.back()" style="color:#555;font-size:12px;">← Back</a>
  <span style="color:#888;font-size:11px;">Tip: In the print dialog choose "Save as PDF" as the destination</span>
</div>

<div class="print-header">
  <div>
    <h1><?= htmlspecialchars($title) ?></h1>
    <p><?= SITE_NAME ?> &bull; <?= SITE_ADDRESS ?></p>
  </div>
  <div class="meta">
    Exported: <?= $exportDate ?><br>
    Total records: <?= count($rows) ?>
  </div>
</div>

<?php if (!$rows): ?>
  <p style="color:#888;padding:20px 0;">No records found.</p>
<?php else: ?>

<table>
  <thead>
    <tr>
      <?php foreach ($headers as $h): ?>
      <th><?= htmlspecialchars($h) ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $row): ?>
    <tr>
      <?php foreach ($fields as $i => $f): ?>
      <td>
        <?php
        $val = htmlspecialchars($row[$f] ?? '');
        // Status badge for applications
        if ($f === 'status' && $val) {
            echo '<span class="badge badge-' . $val . '">' . ucfirst($val) . '</span>';
        }
        // Format currency for fees
        elseif (in_array($f, ['tuition_amount','levies_amount','total_amount'])) {
            echo 'KES ' . number_format((int)($row[$f] ?? 0));
        }
        // Truncate long message text
        elseif ($f === 'message' || $f === 'additional_info') {
            echo nl2br(mb_strimwidth($val, 0, 200, '…'));
        }
        // Date formatting
        elseif ($f === 'created_at' && $val) {
            echo date('d M Y H:i', strtotime($row[$f]));
        }
        elseif ($f === 'date_of_birth' && $val) {
            echo date('d M Y', strtotime($row[$f]));
        }
        else {
            echo $val ?: '—';
        }
        ?>
      </td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php endif; ?>

<div class="footer">
  <span><?= SITE_NAME ?> &bull; <?= SITE_EMAIL ?> &bull; <?= SITE_PHONE ?></span>
  <span>Generated <?= date('d M Y, H:i') ?></span>
</div>

</body>
</html>
<?php
    exit;
}

die('Invalid format. Use: csv or pdf');
