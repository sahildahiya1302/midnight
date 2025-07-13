CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  site_name VARCHAR(255) NOT NULL,
  site_email VARCHAR(255) NOT NULL,
  store_password VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings row if not exists
INSERT INTO settings (id, site_name, site_email) 
SELECT 1, 'My Site', 'admin@example.com' 
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE id = 1);
