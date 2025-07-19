# Midnight Store

This project provides a lightweight PHP storefront and admin panel. Below is the directory layout used throughout the application.

```
/index.php                      - Main storefront entry point
/config.php                     - Constants, DB config, paths
/functions.php                  - Global helper functions
/db.php                         - DB connection (PDO)
/routes.php                     - Simple router
/.htaccess                      - Clean URLs for Apache
/robots.txt                     - Basic robots file
/sitemap.xml                    - Sitemap placeholder

The admin panel features a visual theme editor for arranging sections and blocks.
Developers can modify template files directly using the lightweight **Code Editor** at
`/admin/themes/code-editor.php`.
For a detailed wireframe of the Theme Editor interface, see [docs/theme-editor-wireframe.md](docs/theme-editor-wireframe.md).
Additional UI/UX plans for managing themes and assets are available in
[docs/themes-admin-redesign.md](docs/themes-admin-redesign.md).

/themes/
  /default/
    /layouts/
      theme.php                 - Wrapper for all pages
      password.php              - Password-protected layout
      (visit /password to unlock store)
    /templates/
      index.json                - Homepage layout
      product.json              - Product page
      collection.json           - Collection page
      cart.json                 - Cart page
      search.json               - Search results
      404.json                  - Not found page
      blog.json                 - Blog index
      article.json              - Blog post
      gift_card.json            - Gift card view
      wishlist.json             - Wishlist page
      policies.json             - Terms & Policies
      faq.json                  - FAQ page
      help-center.json          - Help Center
      language.json             - Language settings
      reviews.json              - Reviews
      settings.json             - Account settings
      page.about-us.json
      page.contact.json
      /customers/
        login.json
        register.json
        account.json
        profile.json
        orders.json
        reset_password.json
        addresses.json
    /sections/
      announcement-bar.php
      header.php
      footer.php
      hero-banner.php
      slideshow.php
      image-with-text.php
      product-grid.php
      collection-list.php
      testimonial-slider.php
      newsletter.php
      countdown.php
      featured-product.php
      video-banner.php
      blog-posts.php
      contact-form.php
      faq-section.php
      cart-items.php
      search-results.php
    /snippets/
      product-card.php
      price.php
      compare-price.php
      rating-stars.php
      quantity-selector.php
      swatch.php
      breadcrumbs.php
      pickup-availability.php
      form-input.php
      social-icons.php
      icon.php
      button.php
    /assets/
      theme.css
      theme.js
      custom.js
      grid.css
      reset.css
      /components/
        buttons.css
        forms.css
      /images/
        logo.png
        placeholder.png
        hero.jpg
        favicon.ico
      /fonts/
        montserrat.woff2
      /icons/
        sprite.svg
    /config/
      settings_schema.json
      settings_data.json
    /locales/
      en.json
      hi.json
      fr.json

/admin/
  index.php
Page layouts are stored in `themes/default/templates/*.json`. Each page uses a structured layout object with unique section IDs. Section settings are saved in `themes/default/config/layout_<page>.json`.
  dashboard.php
  products.php
  collections.php
  orders.php
  customers.php
  pages.php
  blogs.php
  discounts.php
  settings.php
  reports.php
  theme-editor.php
  themes.php
  /assets/
    admin.css
    admin.js
    theme-editor.css
    theme-editor.js
  /components/
    nav.php
    header.php
    footer.php
  /modals/
    modal-product.php
    modal-collection.php
    modal-discount.php

/backend/
  /auth/
    login.php
    register.php
    logout.php
    forgot-password.php
    reset-password.php
  /products/
    create.php
    update.php
    delete.php
    list.php
    import.php
  /collections/
    create.php
    update.php
    delete.php
    list.php
  /orders/
    create.php
    update-status.php
    delete.php
    list.php
  /customers/
    create.php
    update.php
    list.php
  /discounts/
    create.php
    update.php
    delete.php
    list.php
  /themes/
    save-layout.php
    load-layout.php
    save-settings.php
    get-schema.php
    upload-image.php
  /search/
    index.php
  /webhooks/
    order-status-updated.php
    product-stock-updated.php
  /utils/
    send-mail.php
    send-sms.php
    compress-image.php
    (uses csrf_token helper for form security)

/api/
  auth.php
  products.php
  orders.php
  customers.php
  theme.php
  discounts.php

/uploads/
  /themes/
    /default/
      /images/
      /fonts/
  /products/
  /banners/
  /avatars/
  /others/
```

This layout mirrors a minimal Shopify-style theme system with a corresponding admin backend.

## Recent Updates

- Added rate limiting and reCAPTCHA verification to admin authentication endpoints.
- Introduced product reviews system with new API (`api/reviews.php`) and backend endpoint (`backend/reviews/create.php`).
- Theme now includes a `product-reviews` section to display customer feedback on product pages.
- Admin login now requires a second-factor verification code sent via email.
- Production `.htaccess` forces HTTPS and disables PHP error display.
- Added wishlist API (`api/wishlist.php`) with theme buttons for users to save favorite products.
- Implemented persistent sessions for carts and new helpers to track recently viewed products.
- Added `recently-viewed` section to display the user's browsing history.
- Introduced product comparison via `api/compare.php` with session-based compare list and a new `compare.json` template.
- Implemented CSRF tokens on authentication forms for increased security.
- Added dynamic product CSV importer with automatic column mapping and sample feed generator.
- Created a dedicated admin page for importing products with drag/drop CSV upload and column mapping preview.
- Added standalone product edit page for managing details, variants and images.
- Added product sets management with API endpoints and admin page to create sets.
- Introduced collection builder pages for creating and editing collections with manual and smart product assignment.
- Implemented product search autocomplete with new `api/search.php` endpoint and header search bar.
- Added a simple page builder storing section layouts in a new `layout` field for pages.
- New theme settings table stores per-theme key/value pairs editable from the Theme Settings page.
- Pages now track versions with `layout_draft` and `layout_published` columns and a new `page_versions` table.
- Added per-page code injection fields and a new `custom-code` section for raw HTML/CSS/JS.
- Introduced section presets and page templates with new admin pages to manage them.
- Added migrations `012_create_section_presets.sql` and `013_create_page_templates.sql`.
- Created basic multi-step checkout flow with cart, address, payment, and success pages.
- Added device-aware rendering via `applyResponsiveSettings` and user agent detection.
- Added sticky add-to-cart bar on product pages for improved mobile UX.
- Added cookie consent banner and localStorage tracking for GDPR compliance.
- Reorganized the admin navigation with new Sales & Marketing and Content & Pages sections.
- Captures UTM campaign parameters into session and stores them on orders (migration `021_add_utm_columns_to_orders.sql`).
- Theme manager now fetches Lighthouse performance data dynamically using the PageSpeed Insights API.

## Database Migrations
Run the SQL files in the `migrations/` directory in order to set up or update the database schema. Recent migrations include `005_extend_product_sets.sql` for product sets, `006_extend_collections.sql` for collection metadata, and `007_create_collection_rules.sql` for smart collection rules.
`009_add_versioning_to_pages.sql` introduces draft/published layouts and version tracking. `010_create_page_versions.sql` stores historical snapshots.
`011_add_custom_code_to_pages.sql` adds head/body/script fields for injecting code per page.
`012_create_section_presets.sql` defines a table for saved section presets.
`013_create_page_templates.sql` defines a table for reusable page templates.
-`018_create_post_order_offers.sql` adds tables for checkout upsell products.
- Implemented email logging and behavior event tracking with new API (`api/events.php`) and migrations for `email_logs`, `user_events`, and `email_campaigns`.
- Checkout success page now reads settings from `checkout_success.json` and supports upsell products.
- Added activity logs for admin actions with new `activity_logs` table and log viewer page.
- Added migration `021_add_utm_columns_to_orders.sql` for storing UTM parameters on orders.
- Added cookie consent banner for GDPR compliance.
- Added additional security headers (Referrer-Policy, Permissions-Policy, X-Permitted-Cross-Domain-Policies)
