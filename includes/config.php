<?php
define('DB_HOST', 'sql_____.epizy.com');  // replace with your InfinityFree DB host
define('DB_USER', 'epiz_XXXXXXX_user');   // replace with your DB username
define('DB_PASS', 'your_password');        // replace with your DB password
define('DB_NAME', 'epiz_XXXXXXX_rapha');  // replace with your full DB name

define('SITE_NAME', 'Rapha Garden School');
define('SITE_TAGLINE', 'Nurturing Excellence, Growing Futures');
define('SITE_PHONE', '+254 722 272 063');
define('SITE_EMAIL', 'info@raphagardenschool.ac.ke');
define('SITE_ADDRESS', 'Athi River, Machakos County, Kenya');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('UPLOADS_URL', '/uploads/');

$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log("DB Connection failed: " . $e->getMessage());
    // Non-fatal — static pages (about, admissions) still render; DB pages show empty state
    $pdo = null;
}

function requireDb(): void {
    global $pdo;
    if (!$pdo) {
        echo "<div style='text-align:center;padding:3rem;color:#888'>Content temporarily unavailable. Please try again later.</div>";
    }
}

session_start();

function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function sanitize(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function flashSet(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flashGet(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
