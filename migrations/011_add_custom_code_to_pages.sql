ALTER TABLE pages
  ADD COLUMN head_code TEXT NULL AFTER layout_published,
  ADD COLUMN body_start_code TEXT NULL AFTER head_code,
  ADD COLUMN body_end_code TEXT NULL AFTER body_start_code,
  ADD COLUMN css_code TEXT NULL AFTER body_end_code,
  ADD COLUMN js_code TEXT NULL AFTER css_code;
