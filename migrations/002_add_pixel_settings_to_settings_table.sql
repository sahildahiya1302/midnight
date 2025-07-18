ALTER TABLE settings
ADD COLUMN enable_facebook_pixel TINYINT(1) DEFAULT 0,
ADD COLUMN facebook_pixel_id VARCHAR(255) DEFAULT NULL,
ADD COLUMN enable_google_analytics TINYINT(1) DEFAULT 0,
ADD COLUMN google_analytics_id VARCHAR(255) DEFAULT NULL,
ADD COLUMN enable_snapchat_pixel TINYINT(1) DEFAULT 0,
ADD COLUMN snapchat_pixel_id VARCHAR(255) DEFAULT NULL;
