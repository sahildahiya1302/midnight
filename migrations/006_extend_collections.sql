ALTER TABLE collections
    ADD COLUMN handle VARCHAR(255) NOT NULL AFTER id,
    ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER description,
    ADD COLUMN status TINYINT(1) DEFAULT 0 AFTER image,
    ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL AFTER status,
    ADD COLUMN meta_description TEXT DEFAULT NULL AFTER meta_title,
    ADD COLUMN canonical_url VARCHAR(255) DEFAULT NULL AFTER meta_description,
    ADD COLUMN og_image VARCHAR(255) DEFAULT NULL AFTER canonical_url;
