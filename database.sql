CREATE DATABASE IF NOT EXISTS blood_finder;
USE blood_finder;
CREATE TABLE donors(
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) not null,
   phone VARCHAR(15) not null,
   blood_group VARCHAR(5) not null,
  city VARCHAR(50) not null,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
INSERT INTO donors (name,phone,blood_group,city) VALUES('harshita koshta','8959245663','O+','jabalpur');
