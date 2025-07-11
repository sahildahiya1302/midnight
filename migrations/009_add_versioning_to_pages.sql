ALTER TABLE pages
    ADD COLUMN layout_draft JSON DEFAULT ('{}') AFTER layout,
    ADD COLUMN layout_published JSON DEFAULT ('{}') AFTER layout_draft,
    ADD COLUMN is_published TINYINT(1) DEFAULT 0 AFTER layout_published,
    ADD COLUMN version INT DEFAULT 1 AFTER is_published;
