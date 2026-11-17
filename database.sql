CREATE DATABASE IF NOT EXISTS blood_bank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blood bank;

CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-') NOT NULL,
    city VARCHAR(100) NOT NULL,
    area VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) NOT NULL,
    last_donated DATE NULL,
    available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_blood_city (blood_group, city)
);

INSERT INTO donors (name, blood_group, city, area, phone, last_donated, available) VALUES
('Aarav Sharma', 'O+', 'Jabalpur', 'Adhartal', '+91 98765 43210', '2026-06-15', 1),
('Priya Patel', 'A+', 'Bhopal', 'MP Nagar', '+91 98234 56789', '2026-05-10', 1),
('Rohan Verma', 'O-', 'Indore', 'Vijay Nagar', '+91 91234 56780', '2026-04-20', 1),
('Sneha Gupta', 'B+', 'Jabalpur', 'Ranjhi', '+91 99887 76655', '2026-03-12', 1),
('Vikram Thakur', 'A+', 'Jabalpur', 'Civil Lines', '+91 97654 32109', '2026-02-18', 1),
('Ananya Tiwari', 'O-', 'Jabalpur', 'Vijay Nagar', '+91 96543 21098', NULL, 1);
