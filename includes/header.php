<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' . SITE_NAME : SITE_NAME ?></title>
  <meta name="description" content="<?= isset($pageDesc) ? sanitize($pageDesc) : 'Rapha Garden School - Nurturing Excellence, Growing Futures in Athi River, Machakos County, Kenya.' ?>">
  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
</head>
<body>

<!-- Top Bar -->
<div class="topbar">
  <div class="container topbar-inner">
    <span>📍 <?= SITE_ADDRESS ?></span>
    <span>📞 <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a> &nbsp;|&nbsp; ✉️ <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></span>
  </div>
</div>

<!-- Main Navigation -->
<header class="navbar" id="navbar">
  <div class="container nav-inner">
    <!-- Logo -->
    <a href="/" class="nav-logo">
      <div class="logo-icon">RG</div>
      <div>
        <div class="logo-name"><?= SITE_NAME ?></div>
        <div class="logo-tagline"><?= SITE_TAGLINE ?></div>
      </div>
    </a>

    <!-- Hamburger -->
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>

    <!-- Nav Links -->
    <nav class="nav-links" id="navLinks">
      <a href="/" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Home</a>

      <div class="nav-dropdown">
        <a href="/about" class="nav-item has-dropdown <?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">
          About <span class="chevron">▾</span>
        </a>
        <div class="dropdown-menu">
          <a href="/about#story">Our Story</a>
          <a href="/about#vision">Vision &amp; Mission</a>
          <a href="/about#team">Our Team</a>
        </div>
      </div>

      <div class="nav-dropdown">
        <a href="/admissions" class="nav-item has-dropdown <?= basename($_SERVER['PHP_SELF']) === 'admissions.php' ? 'active' : '' ?>">
          Admissions <span class="chevron">▾</span>
        </a>
        <div class="dropdown-menu">
          <a href="/admissions#how-to-apply">How to Apply</a>
          <a href="/admissions#requirements">Requirements</a>
          <a href="/admissions#forms">Download Forms</a>
        </div>
      </div>

      <a href="/fees" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'fees.php' ? 'active' : '' ?>">Fees</a>

      <a href="/gallery" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'gallery.php' ? 'active' : '' ?>">Gallery</a>

      <div class="nav-dropdown">
        <a href="/news" class="nav-item has-dropdown <?= basename($_SERVER['PHP_SELF']) === 'news.php' ? 'active' : '' ?>">
          News <span class="chevron">▾</span>
        </a>
        <div class="dropdown-menu">
          <a href="/news?cat=news">Latest News</a>
          <a href="/news?cat=events">Events</a>
          <a href="/news?cat=notices">Notices</a>
        </div>
      </div>

      <a href="/contact" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>">Contact</a>

      <a href="/apply" class="btn-apply">Apply Now</a>
    </nav>
  </div>
</header>
