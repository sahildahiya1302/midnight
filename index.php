<?php
declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/routes.php';
require __DIR__ . '/theme-engine.php';

session_start();
captureUTMParams();
setSecurityHeaders();

$route = resolveCurrentRoute();
$previewPage = $_GET['page'] ?? null;

// Live Preview via Theme Editor
if ($previewPage && preg_match('/^[a-z0-9_-]+$/i', $previewPage)) {
    $pageTitle = ucfirst(str_replace('-', ' ', $previewPage));
    $layoutData = loadThemeLayout($previewPage);
    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

// 404 fallback
error_log("Resolved route: " . $route);
if ($route === '404') {
    http_response_code(404);
    $layoutData = loadThemeLayout('404');
    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

// SEO & page meta defaults
$pageTitle = null;
$pageDescription = null;
$ogImage = asset('images/logo.png');
$pageHeadCode = '';
$pageBodyStartCode = '';
$pageBodyEndCode = '';
$pageCssCode = '';
$pageJsCode = '';

// ✅ Homepage (from index.json)
if ($route === 'index') {
    // Load the default layout
    $layoutData = loadThemeLayout('index');

    // Fetch presets from backend and inject into announcement-bar tiles
    $presetsJson = file_get_contents(__DIR__ . '/backend/presets/list.php');
    $presets = [];
    if ($presetsJson) {
        $presets = json_decode($presetsJson, true);
    }

    if (!empty($presets) && isset($layoutData['sections'])) {
        foreach ($layoutData['sections'] as $sectionId => &$section) {
            if ($section['type'] === 'announcement-bar') {
                $section['blocks'] = [];
                foreach ($presets as $preset) {
                    $section['blocks'][] = [
                        'type' => 'tile',
                        'settings' => [
                            'text' => $preset['text'] ?? '',
                            'link' => $preset['link'] ?? '#',
                            'background_color' => $preset['background_color'] ?? '#000000',
                            'dismissible' => $preset['dismissible'] ?? false,
                            'text_color' => $preset['text_color'] ?? '#ffffff',
                            'padding' => $preset['padding'] ?? '0 1rem',
                            'font_size' => $preset['font_size'] ?? '1rem',
                            'font_weight' => $preset['font_weight'] ?? '600',
                        ],
                    ];
                }
                break;
            }
        }
        unset($section);
    }

    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

// Cart (static PHP)
if ($route === 'cart') {
    $template = 'themes/default/templates/cart.php';
    if (is_file($template)) {
        $content = render($template);
        include THEME_PATH . '/layouts/theme.php';
        exit;
    }
    notFound();
}

// Product (from JSON)
if (str_starts_with($route, 'product:')) {
    $handle = substr($route, 8);
    $context['product'] = getProductByHandle($handle);
    if (!$context['product']) {
        notFound();
    }
    // Add recently viewed product tracking
    addToRecentlyViewed($context['product']['id']);
    $layoutData = loadThemeLayout('product');
    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

// Collection (from JSON)
if (str_starts_with($route, 'collection:')) {
    $slug = substr($route, 11);
    $context['collection'] = getCollectionBySlug($slug);
    if (!$context['collection']) {
        notFound();
    }
    $layoutData = loadThemeLayout('collection');
    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

// Search (from JSON)
if ($route === 'search') {
    $layoutData = loadThemeLayout('search');
    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

// Store password protection
$adminRoutePrefix = 'admin';
$backendRoutePrefix = 'backend';
$storePasswordData = getStorePassword();

define('STORE_PASSWORD_ENABLED', $storePasswordData['enabled']);
define('STORE_PASSWORD', $storePasswordData['password']);

if (STORE_PASSWORD_ENABLED && STORE_PASSWORD && empty($_SESSION['store_unlocked']) &&
    $route !== 'php:' . THEME_PATH . '/layouts/password.php' &&
    !str_starts_with($route, $adminRoutePrefix) &&
    !str_starts_with($route, $backendRoutePrefix)) {
    include THEME_PATH . '/layouts/password.php';
    exit;
}

// PHP templates
if (str_starts_with($route, 'php:')) {
    $file = substr($route, 4);
    if (is_file($file)) {
        $content = render($file);
        include THEME_PATH . '/layouts/theme.php';
        exit;
    }
    notFound();
}

// Catch-all: JSON or PHP template
$templateBase = 'themes/default/templates/' . $route;

if (is_file($templateBase . '.json')) {
    $layoutData = loadThemeLayout($route);
    $content = renderLayoutArray($layoutData);
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

if (is_file($templateBase . '.php')) {
    $content = render($templateBase . '.php');
    include THEME_PATH . '/layouts/theme.php';
    exit;
}

notFound();
