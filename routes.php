<?php
declare(strict_types=1);

/**
 * Resolve the current request path to a template name or PHP file.
 * Returns a string like 'index' for templates or 'php:themes/default/templates/cart.php'
 * for direct PHP pages or '404' if no match.
 */
function resolveCurrentRoute(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = rtrim($path, '/');
    if ($path === '') {
        return 'index';
    }

    // Static PHP template routes
    $phpRoutes = [
        '/cart' => 'php:themes/default/templates/cart.php',
        '/checkout/cart' => 'php:themes/default/templates/checkout/cart.php',
        '/checkout/address' => 'php:themes/default/templates/checkout/address.php',
        '/checkout/payment' => 'php:themes/default/templates/checkout/payment.php',
        '/checkout/success' => 'php:themes/default/templates/checkout/success.php',
        '/compare' => 'php:themes/default/templates/compare.php',
        '/password' => 'php:' . THEME_PATH . '/layouts/password.php',
        '/account/login' => 'php:themes/default/templates/customers/login.php',
    ];

    if (isset($phpRoutes[$path])) {
        return $phpRoutes[$path];
    }

    // JSON layout templates
    $jsonRoutes = [
        '/search' => 'search',
        '/page/about-us' => 'page.about-us',
        '/page/contact' => 'page.contact',
    ];

    if (isset($jsonRoutes[$path])) {
        return $jsonRoutes[$path];
    }

    if (preg_match('#^/page/([^/]+)$#', $path, $m)) {
        return 'dbpage:' . $m[1];
    }

    // Dynamic product or collection page
    if (preg_match('#^/products/([^/]+)$#', $path, $m)) {
        return 'product:' . $m[1];
    }
    if (preg_match('#^/collections/([^/]+)$#', $path, $m)) {
        return 'collection:' . $m[1];
    }

    // Catch: check for a real JSON file in templates
    $slug = ltrim($path, '/');
    $jsonFile = __DIR__ . '/themes/default/templates/' . $slug . '.json';
    if (is_file($jsonFile)) {
        return $slug;
    }

    return '404';
}
