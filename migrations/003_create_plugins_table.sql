CREATE TABLE IF NOT EXISTS plugins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  version VARCHAR(50),
  enabled TINYINT(1) DEFAULT 0,
  installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
