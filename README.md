# NovaCorp HR Portal
CYS 538: Web Technology and Security 

---

## Requirements
- Ubuntu 22.04
- Apache2, MySQL, PHP (LAMP Stack)

---

## Installation

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql -y
sudo cp -r hr_portal/ /var/www/html/
sudo chmod 777 /var/www/html/hr_portal/uploads
sudo chmod 777 /var/www/html/hr_portal/logs
```

---

## Database Setup

```bash
sudo mysql
```

```sql
CREATE USER 'hruser'@'localhost' IDENTIFIED BY 'Hr@123456';
CREATE DATABASE hr_portal;
GRANT ALL PRIVILEGES ON hr_portal.* TO 'hruser'@'localhost';
FLUSH PRIVILEGES;
USE hr_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','employee','applicant') DEFAULT 'applicant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    department VARCHAR(50),
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    job_id INT,
    cv_filename VARCHAR(255),
    status ENUM('pending','reviewed','accepted','rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (job_id) REFERENCES jobs(id)
);

INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@novacorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO jobs (title, description, department) VALUES
('Cybersecurity Analyst', 'Monitoring and protecting company networks from cyber threats.', 'IT Security'),
('HR Business Partner', 'Develop and implement HR strategies across departments.', 'Human Resources'),
('DevOps Engineer', 'CI/CD pipelines and cloud infrastructure management.', 'Engineering'),
('Data Analyst', 'Business data analysis and dashboard reporting.', 'Business Intelligence'),
('Software Engineer', 'Web application development using modern frameworks.', 'Engineering');
```

---

## Default Login

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `password` |
| Applicant | Register new account | — |

---

## LFI Attack Demo

```
# Vulnerable
http://localhost/hr_portal/view.php?file=../../../../etc/passwd
http://localhost/hr_portal/view.php?file=../db.php

# Secure (countermeasure)
http://localhost/hr_portal/view_secure.php?file=../../../../etc/passwd
```
