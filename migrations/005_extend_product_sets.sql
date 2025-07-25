ALTER TABLE product_sets
    ADD COLUMN handle VARCHAR(255) NOT NULL AFTER name,
    ADD COLUMN description TEXT NULL AFTER handle,
    ADD COLUMN price DECIMAL(10,2) DEFAULT NULL AFTER description,
    ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER price,
    ADD COLUMN status TINYINT(1) DEFAULT 0 AFTER image;
