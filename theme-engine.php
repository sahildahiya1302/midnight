<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

/**
 * Renders a template from a JSON file using layout array structure.
 *
 * @param string $templateName The template slug (e.g., 'index', 'product')
 * @return string HTML output of the full layout
 */
function renderTemplate(string $templateName): string
{
    $layout = loadThemeLayout($templateName);
    return renderLayoutArray($layout);
}

/**
 * For legacy support: render and include the full page via template
 *
 * @param string $templateName
 * @return void
 */
function renderPage(string $templateName): void
{
    $content_for_layout = renderTemplate($templateName);
    include THEME_PATH . '/layouts/theme.php';
}

/**
 * Includes a section PHP file with settings + blocks
 *
 * @param string $type
 * @param array $data
 */

