<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

if (!function_exists('render')) {
    function render(string $template, array $data = []): string
    {
        extract($data);
        ob_start();
        include BASE_PATH . '/' . ltrim($template, '/');
        return ob_get_clean();
    }
}

/**
 * Output a template directly.
 */
if (!function_exists('view')) {
    function view(string $template, array $data = []): void
    {
        echo render($template, $data);
    }
}

/**
 * Escape output for HTML.
 */
if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Send JSON response.
 */
if (!function_exists('jsonResponse')) {
    function jsonResponse($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

/**
 * Get or generate a CSRF token stored in the session.
 */
if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf_token'];
    }
}

/**
 * Verify a CSRF token from user input.
 */
if (!function_exists('verify_csrf')) {
    function verify_csrf(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}


if (!function_exists('verify_recaptcha')) {
    function verify_recaptcha(string $token): bool
    {
        $secret = getenv('RECAPTCHA_SECRET');
        if (!$secret || !$token) {
            return true;
        }
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]));
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === false) {
            return false;
        }
        $data = json_decode($result, true);
        return !empty($data['success']);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Generate an asset URL for the active theme.
 */
if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/themes/' . THEME . '/assets/' . ltrim($path, '/');
    }
}

/**
 * Include a theme section file if it exists.
 */
if (!function_exists('includeSection')) {
    function includeSection(string $name, array $data = []): void
    {
        $file = THEME_PATH . '/sections/' . $name . '.php';
        if (!is_file($file)) {
            echo "<!-- Section file '{$name}.php' not found -->";
            return;
        }

        // Generate a unique id if none provided
        $id = $data['id'] ?? ($name . '-' . uniqid());
        $blocks = $data['blocks'] ?? [];

        // Remove non-setting keys before merging with defaults
        $settingData = $data;
        unset($settingData['id'], $settingData['blocks']);

        // Load defaults from schema JSON if available
        $schemaFile = THEME_PATH . '/sections/' . $name . '.schema.json';
        $defaults = [];
        if (is_file($schemaFile)) {
            $schema = json_decode(file_get_contents($schemaFile), true);
            foreach (($schema['settings'] ?? []) as $setting) {
                if (isset($setting['id'])) {
                    $defaults[$setting['id']] = $setting['default'] ?? null;
                }
            }
        }

        $settings = array_merge($defaults, $settingData);

        // Make variables available to the section template
        include $file;
    }
}

/**
 * Load theme settings from settings_data.json.
 */
if (!function_exists('getSetting')) {
    function getSetting(string $key, $default = null)
    {
        static $settings;
        if ($settings === null) {
            $file = THEME_PATH . '/config/settings_data.json';
            $settings = is_file($file) ? json_decode(file_get_contents($file), true) : [];

            try {
                $themeId = db_query('SELECT id FROM themes WHERE active = 1 LIMIT 1')->fetchColumn();
                if ($themeId) {
                    $rows = db_query('SELECT `key`, `value` FROM theme_settings WHERE theme_id = :tid', [':tid' => $themeId])->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $row) {
                        $settings[$row['key']] = $row['value'];
                    }
                }
            } catch (Throwable $e) {
                // ignore if table missing
            }
        }
        return $settings[$key] ?? $default;
    }
}

/**
 * Get the active color scheme values.
 */
function getColorScheme(): array
{
    $schemeName = getSetting('color_scheme', 'default');
    $file = THEME_PATH . '/config/color_schemes.json';
    $schemes = is_file($file) ? json_decode(file_get_contents($file), true) : [];
    return $schemes[$schemeName] ?? ($schemes['default'] ?? []);
}

/**
 * Retrieve a menu by handle from theme settings.
 *
 * Menus are stored in settings_data.json under the "menus" key
 * as an associative array of menu handles to item arrays.
 */
if (!function_exists('getMenu')) {
    function getMenu(string $handle): array
    {
        $menus = getSetting('menus', []);
        return $menus[$handle] ?? [];
    }
}

/**
 * Render a snippet from the theme snippets directory.
 */
if (!function_exists('renderPartial')) {
    function renderPartial(string $name, array $data = []): string
    {
        $file = THEME_PATH . '/snippets/' . $name . '.php';
        if (!is_file($file)) {
            return '';
        }
        extract($data);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}

/**
 * Send a 404 response and render the 404 template.
 */
if (!function_exists('notFound')) {
    function notFound(): void
    {
        http_response_code(404);
        $template = THEME_PATH . '/templates/404.php';
        if (is_file($template)) {
            $content = render($template);
            include THEME_PATH . '/layouts/theme.php';
            exit;
        } else {
            echo "404 Not Found";
            exit;
        }
    }
}

/**
 * Load locale strings for the given code.
 */
if (!function_exists('loadLocale')) {
    function loadLocale(string $code = 'en'): array
    {
        static $cache = [];
        if (!isset($cache[$code])) {
            $file = THEME_PATH . '/locales/' . $code . '.json';
            $cache[$code] = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        }
        return $cache[$code];
    }
}

/**
 * Translate key using loaded locale strings.
 */
if (!function_exists('t')) {
    function t(string $key, array $locale): string
    {
        return $locale[$key] ?? $key;
    }
}

if (!function_exists('getCurrentPage')) {
    function getCurrentPage(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        return trim($uri, '/') ?: 'index';
    }
}

/**
 * Fetch a single product by ID.
 */
if (!function_exists('getProduct')) {
    function getProduct(int $id): ?array
    {
        $stmt = db_query('SELECT * FROM products WHERE id = :id', [':id' => $id]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}

if (!function_exists('listThemeSections')) {
    function listThemeSections(string $theme = THEME): array
    {
        $path = __DIR__ . "/themes/{$theme}/sections";
        if (!is_dir($path)) {
            return [];
        }
        $files = glob($path . '/*.php');
        $names = [];
        foreach ($files as $file) {
            $names[] = basename($file, '.php');
        }
        sort($names);
        return $names;
    }
}


if (!function_exists('saveThemeLayout')) {
    function saveThemeLayout(string $page, array $layout, string $theme = THEME): bool
    {
        $file = __DIR__ . "/themes/{$theme}/templates/{$page}.json";
        $json = json_encode($layout, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return file_put_contents($file, $json) !== false;
    }
}

if (!function_exists('getThemeConfig')) {
    function getThemeConfig(): array
    {
        static $themeConfig;
        if ($themeConfig === null) {
            $file = THEME_PATH . '/theme.json';
            $themeConfig = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        }
        return $themeConfig;
    }
}

if (!function_exists('getCheckoutSettings')) {
    function getCheckoutSettings(): array
    {
        static $settings;
        if ($settings === null) {
            $file = THEME_PATH . '/config/checkout_settings.json';
            $settings = is_file($file) ? json_decode(file_get_contents($file), true) : [];
        }
        return $settings;
    }
}

if (!function_exists('renderLayoutArray')) {
    /**
     * Render a page layout from a layout array (with sections and order).
     */
function renderLayoutArray(array $layout): string
{
    $output = '';

    if (!isset($layout['order']) || !is_array($layout['order'])) return $output;
    if (!isset($layout['sections']) || !is_array($layout['sections'])) return $output;

    foreach ($layout['order'] as $sectionId) {
        $section = $layout['sections'][$sectionId] ?? null;
        if (!$section || !is_array($section) || !array_key_exists('type', $section)) continue;

        $type = $section['type'];
        $settings = $section['settings'] ?? [];
        $blocks = $section['blocks'] ?? [];
        $id = $sectionId;

        $sectionFile = THEME_PATH . "/sections/{$type}.php";
        if (is_file($sectionFile)) {
            ob_start();
            // Scope variables for section rendering
            include $sectionFile;
            $output .= ob_get_clean();
        } else {
            $output .= "<!-- Section '{$type}' not found -->";
        }
    }

    return $output;
}
}


function loadThemeLayout(string $page, string $theme = THEME): array {

    $slug = $page;

    // Check session for live preview data
    if (isset($_GET['preview']) && isset($_SESSION['live_preview'][$slug])) {
        return convertEditorLayoutToCompiled($_SESSION['live_preview'][$slug]);
    }

    $file = __DIR__ . "/themes/{$theme}/templates/{$slug}.json";
    if (!is_file($file)) {
        return ['sections' => [], 'order' => []];
    }

    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : ['sections' => [], 'order' => []];
}
function convertEditorLayoutToCompiled(array $layout): array {
    $compiled = ['sections' => [], 'order' => []];
    foreach ($layout as $section) {
        $id = $section['id'] ?? uniqid($section['type'] . '-');
        $compiled['sections'][$id] = [
            'type' => $section['type'],
            'settings' => $section['settings'] ?? [],
            'blocks' => $section['blocks'] ?? [],
        ];
        $compiled['order'][] = $id;
    }
    return $compiled;
}




if (!function_exists('getProductByHandle')) {
    function getProductByHandle(string $handle): ?array
    {
        $stmt = db_query('SELECT * FROM products WHERE handle = :handle LIMIT 1', [':handle' => $handle]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}

if (!function_exists('getProductVariants')) {
    /**
     * Fetch variants for a product.
     */
    function getProductVariants(int $productId): array
    {
        $stmt = db_query('SELECT * FROM product_variants WHERE product_id = :pid ORDER BY id', [':pid' => $productId]);
        return $stmt->fetchAll();
    }
}

if (!function_exists('getCollectionBySlug')) {
    function getCollectionBySlug(string $slug): ?array
    {
        $stmt = db_query('SELECT * FROM collections WHERE slug = :slug LIMIT 1', [':slug' => $slug]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }
}

if (!function_exists('getPageBySlug')) {
    function getPageBySlug(string $slug): ?array
    {
        $stmt = db_query('SELECT * FROM pages WHERE slug = :slug LIMIT 1', [':slug' => $slug]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }
        // Fallback for older schema
        if (!isset($row['layout_published'])) {
            $row['layout_published'] = $row['layout'] ?? '{}';
        }
        return $row;
    }
}

/**
 * Fetch multiple products by ID list, preserving input order.
 */
if (!function_exists('getProducts')) {
function getProducts(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = db_query('SELECT * FROM products WHERE id IN (' . $placeholders . ')', $ids);
        $rows = $stmt->fetchAll();
        // index by id for easier ordering
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['id']] = $row;
        }
        $ordered = [];
        foreach ($ids as $id) {
            if (isset($indexed[$id])) {
                $ordered[] = $indexed[$id];
            }
        }
        return $ordered;
    }
}

if (!function_exists('getProductsByCollection')) {
    /**
     * Fetch products belonging to a collection with optional limit.
     */
function getProductsByCollection($collectionId, $limit = 8) {
    $limit = intval($limit); // Ensure it's a valid number
    $sql = "
      SELECT p.*
      FROM products p
      JOIN collection_product cp ON cp.product_id = p.id
      WHERE cp.collection_id = :collection_id
      LIMIT $limit
    ";
    $stmt = db_query($sql, [
        'collection_id' => $collectionId
    ]);
    return $stmt->fetchAll();
}

function getProductReviews($productId) {
    $sql = "SELECT rating FROM product_reviews WHERE product_id = ?";
    return db_query($sql, [$productId]);
}

function getCollectionsForProduct(int $productId): array {
    $sql = "SELECT c.* FROM collections c JOIN collection_product cp ON cp.collection_id = c.id WHERE cp.product_id = :pid";
    $stmt = db_query($sql, [':pid' => $productId]);
    return $stmt->fetchAll();
}

function getFirstCollectionIdForProduct(int $productId): ?int {
    $sql = "SELECT collection_id FROM collection_product WHERE product_id = :pid ORDER BY collection_id ASC LIMIT 1";
    $stmt = db_query($sql, [':pid' => $productId]);
    $row = $stmt->fetch();
    return $row ? (int)$row['collection_id'] : null;
}

if (!function_exists('getProductsBySet')) {
    /**
     * Fetch products belonging to a product set with optional limit.
     */
    function getProductsBySet($setId, $limit = 8) {
        $limit = intval($limit);
        $sql = "
          SELECT p.*
          FROM products p
          JOIN product_set_products sp ON sp.product_id = p.id
          WHERE sp.set_id = :sid
          LIMIT $limit
        ";
        $stmt = db_query($sql, ['sid' => $setId]);
        return $stmt->fetchAll();
    }
}



}

if (!function_exists('generateHandle')) {
    function generateHandle(string $title): string
    {
        $handle = strtolower($title);
        $handle = preg_replace('/[^a-z0-9-]+/', '-', $handle);
        $handle = trim($handle, '-');
        return $handle;
    }
}

if (!function_exists('db_last_insert_id')) {
    function db_last_insert_id(): string
    {
        return db()->lastInsertId();  // ✅ Use the db() helper from db.php
    }
}

if (!function_exists('getPrimaryProductImage')) {
    function getPrimaryProductImage(int $productId): ?string
    {
        $stmt = db_query('SELECT src FROM product_images WHERE product_id = :pid ORDER BY id ASC LIMIT 1', [':pid' => $productId]);
        $row = $stmt->fetch();
        return $row['src'] ?? null;
    }
}


// -------------------------
// Product-related Functions
// -------------------------

function getRelatedProductsByCollection($collectionId, $excludeProductId, $limit = 6) {
    $limit = intval($limit);
    $sql = "
      SELECT p.*
      FROM products p
      JOIN collection_product cp ON cp.product_id = p.id
      WHERE cp.collection_id = :collection_id
        AND p.id != :exclude_id
      ORDER BY RAND()
      LIMIT $limit
    ";
    return db_query($sql, [
        'collection_id' => $collectionId,
        'exclude_id' => $excludeProductId
    ]);
}

function getFallbackRecommendedProducts($limit = 6) {
    $limit = intval($limit);
    $sql = "SELECT * FROM products ORDER BY RAND() LIMIT $limit";
    return db_query($sql);
}


// ---------- Wishlist Helpers ----------
if (!function_exists('addToWishlist')) {
    function addToWishlist(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['wishlist'] = $_SESSION['wishlist'] ?? [];
        if (!in_array($productId, $_SESSION['wishlist'], true)) {
            $_SESSION['wishlist'][] = $productId;
        }
    }
}

if (!function_exists('removeFromWishlist')) {
    function removeFromWishlist(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['wishlist'] = $_SESSION['wishlist'] ?? [];
        $_SESSION['wishlist'] = array_values(array_filter(
            $_SESSION['wishlist'],
            fn($id) => (int)$id !== $productId
        ));
    }
}

if (!function_exists('getWishlist')) {
    function getWishlist(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return array_map('intval', $_SESSION['wishlist'] ?? []);
    }
}

// ---------- Compare Helpers ----------
if (!function_exists('addToCompare')) {
    function addToCompare(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['compare'] = $_SESSION['compare'] ?? [];
        if (!in_array($productId, $_SESSION['compare'], true)) {
            $_SESSION['compare'][] = $productId;
        }
    }
}

if (!function_exists('removeFromCompare')) {
    function removeFromCompare(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['compare'] = $_SESSION['compare'] ?? [];
        $_SESSION['compare'] = array_values(array_filter(
            $_SESSION['compare'],
            fn($id) => (int)$id !== $productId
        ));
    }
}

if (!function_exists('getCompareList')) {
    function getCompareList(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return array_map('intval', $_SESSION['compare'] ?? []);
    }
}

// ---------- Recently Viewed Helpers ----------
if (!function_exists('addToRecentlyViewed')) {
    function addToRecentlyViewed(int $productId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['recently_viewed'] = $_SESSION['recently_viewed'] ?? [];
        $_SESSION['recently_viewed'] = array_values(array_filter(
            array_unique(array_merge([$productId], $_SESSION['recently_viewed'])),
            fn($id) => $id > 0
        ));
        $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 10);
    }
}

if (!function_exists('getRecentlyViewed')) {
    function getRecentlyViewed(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return array_map('intval', $_SESSION['recently_viewed'] ?? []);
    }
}

if (!function_exists('slugify')) {
    function slugify(string $string): string
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9]+/i', '-', $string);
        return trim($string, '-');
    }
}

if (!function_exists('parse_price')) {
    /**
     * Normalize a price string to a float value.
     * Strips currency symbols and thousands separators.
     */
    function parse_price(string $value): float
    {
        $normalized = preg_replace('/[^0-9.]/', '', $value);
        return (float) $normalized;
    }
}

if (!function_exists('captureUTMParams')) {
    function captureUTMParams(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $params = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content'];
        foreach ($params as $p) {
            if (!empty($_GET[$p])) {
                $_SESSION['utm'][$p] = $_GET[$p];
            }
        }
    }
}

if (!function_exists('getUTMParams')) {
    function getUTMParams(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['utm'] ?? [];
    }
}

if (!function_exists('isMobile')) {
    function isMobile(): bool
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return preg_match('/(android|iphone|ipod|blackberry|mobile)/i', $ua) === 1;
    }
}

if (!function_exists('isTablet')) {
    function isTablet(): bool
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return preg_match('/(ipad|tablet)/i', $ua) === 1;
    }
}

if (!function_exists('currentDevice')) {
    function currentDevice(): string
    {
        if (isMobile()) {
            return 'mobile';
        }
        if (isTablet()) {
            return 'tablet';
        }
        return 'desktop';
    }
}

if (!function_exists('applyResponsiveSettings')) {
    function applyResponsiveSettings(array $settings): array
    {
        $device = currentDevice();
        foreach ($settings as $k => $v) {
            if (is_array($v) && isset($v['desktop']) ) {
                $settings[$k] = $v[$device] ?? ($v['desktop'] ?? null);
            } elseif (is_array($v)) {
                $settings[$k] = applyResponsiveSettings($v);
            }
        }
        return $settings;
    }
}

if (!function_exists('setSecurityHeaders')) {
    /**
     * Send common security headers to harden the application.
     */
    function setSecurityHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: no-referrer-when-downgrade');
        header('X-Permitted-Cross-Domain-Policies: none');
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
        if (isset($_SERVER['HTTPS'])) {
            header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
        }
        header("Content-Security-Policy: default-src 'self' https://www.googletagmanager.com https://connect.facebook.net; img-src 'self' data: https:; script-src 'self' https://www.googletagmanager.com https://connect.facebook.net; style-src 'self' 'unsafe-inline'");
    }
}

if (!function_exists('send_mail')) {
    function send_mail(string $to, string $subject, string $message, string $from = 'no-reply@example.com'): bool
    {
        $headers = "From: {$from}\r\n" .
                   "Reply-To: {$from}\r\n" .
                   "X-Mailer: PHP/" . phpversion();
        $sent = mail($to, $subject, $message, $headers);
        $status = $sent ? 'sent' : 'failed';
        db_query('INSERT INTO email_logs (to_email, subject, status, error_message) VALUES (:to_email, :subject, :status, :error)', [
            ':to_email' => $to,
            ':subject' => $subject,
            ':status' => $status,
            ':error' => $sent ? null : 'mail() failed'
        ]);
        return $sent;
    }
}

if (!function_exists('track_event')) {
    function track_event(?int $userId, string $eventType, array $eventData = []): void
    {
        $sessionId = session_id();
        db_query('INSERT INTO user_events (user_id, session_id, event_type, event_data) VALUES (:uid, :sid, :type, :data)', [
            ':uid' => $userId,
            ':sid' => $sessionId,
            ':type' => $eventType,
            ':data' => json_encode($eventData)
        ]);
    }
}

if (!function_exists('getGlobalSection')) {
    function getGlobalSection(string $handle): ?array
    {
        static $cache = [];
        if (isset($cache[$handle])) {
            return $cache[$handle];
        }
        $stmt = db_query('SELECT type, section_json FROM global_sections WHERE handle = :h LIMIT 1', [':h' => $handle]);
        $row = $stmt->fetch();
        if ($row) {
            $data = json_decode($row['section_json'], true) ?: [];
            $cache[$handle] = [
                'type' => $row['type'],
                'settings' => $data['settings'] ?? [],
                'blocks' => $data['blocks'] ?? []
            ];
            return $cache[$handle];
        }
        return null;
    }
}

if (!function_exists('getPostOrderOffers')) {
    function getPostOrderOffers(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = db()->prepare("SELECT * FROM post_order_offers WHERE product_id IN ($placeholders)");
        $stmt->execute($productIds);
        return $stmt->fetchAll();
    }
}



if (!function_exists("log_activity")) {
    /**
     * Record an admin action in the activity_logs table.
     */
    function log_activity(?int $userId, string $action, array $metadata = []): void
    {
        db_query("INSERT INTO activity_logs (user_id, action, metadata) VALUES (:uid, :action, :meta)", [
            ":uid" => $userId,
            ":action" => $action,
            ":meta" => json_encode($metadata)
        ]);
    }

};


function check_admin_logged_in(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header("Location: /backend/auth/login.php");
        exit;
    }
}
