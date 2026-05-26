<aside class="admin-sidebar">
  <div class="logo">
    <h2><?= SITE_NAME ?></h2>
    <p>Admin Panel</p>
  </div>
  <nav class="admin-nav">
    <?php $page = basename($_SERVER['PHP_SELF']); ?>
    <a href="/admin/dashboard.php"     class="<?= $page==='dashboard.php'    ?'active':'' ?>">📊 Dashboard</a>
    <a href="/admin/applications.php"  class="<?= $page==='applications.php' ?'active':'' ?>">📋 Applications</a>
    <a href="/admin/gallery.php"       class="<?= $page==='gallery.php'      ?'active':'' ?>">📸 Gallery</a>
    <a href="/admin/news.php"          class="<?= $page==='news.php'         ?'active':'' ?>">📰 News &amp; Events</a>
    <a href="/admin/fees.php"          class="<?= $page==='fees.php'         ?'active':'' ?>">💰 Fee Structure</a>
    <a href="/admin/team.php"          class="<?= $page==='team.php'         ?'active':'' ?>">👥 Our Team</a>
    <a href="/admin/forms.php"         class="<?= $page==='forms.php'        ?'active':'' ?>">📄 Download Forms</a>
    <a href="/admin/contacts.php"      class="<?= $page==='contacts.php'     ?'active':'' ?>">📩 Messages</a>
    <a href="/" target="_blank" style="margin-top:auto;border-top:1px solid rgba(255,255,255,0.1);padding-top:0.85rem;">🌐 View Website</a>
    <a href="/admin/logout.php" style="color:#ef9a9a;">🚪 Logout</a>
  </nav>
</aside>
