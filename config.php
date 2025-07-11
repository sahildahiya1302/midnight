<?php
// config.php (SAFE version — no early DB call)
declare(strict_types=1);

// Extend session cookie lifetime to keep carts persistent for 30 days


// Load .env
$dotenvPath = __DIR__ . '/.env';
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        putenv($line);
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $_ENV[trim($parts[0])] = trim($parts[1]);
            $_SERVER[trim($parts[0])] = trim($parts[1]);
        }
    }
}

define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASSWORD'));
define('DB_CHARSET', 'utf8mb4');
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);

define('BASE_PATH', __DIR__);
define('BASE_URL', getenv('BASE_URL') ?: '');
define('THEME', 'default');
define('THEME_PATH', BASE_PATH . '/themes/' . THEME);
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define("RECAPTCHA_SECRET", getenv("RECAPTCHA_SECRET") ?: "");
define("RECAPTCHA_SITE_KEY", getenv("RECAPTCHA_SITE_KEY") ?: "");

// Function is defined, but not executed here
function getStorePassword() {
    $stmt = db()->prepare('SELECT store_password, store_password_enabled FROM settings WHERE id = 1');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return [
        'password' => $result['store_password'] ?? null,
        'enabled' => !empty($result['store_password_enabled']),
    ];
}
