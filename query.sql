CREATE DATABASE crm_db;

USE crm_db;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    company VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('Active','Prospect','Inactive') NOT NULL,
    last_contact DATE DEFAULT CURRENT_DATE
);

CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id VARCHAR(50) NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Active','Expired','Pending Renewal') NOT NULL,
    sla_compliance VARCHAR(10) NOT NULL 
)

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    uploaded_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Compliant'
);
CREATE TABLE crm2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    company VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    status ENUM('Active', 'Inactive', 'Prospect') NOT NULL DEFAULT 'Prospect',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- dashboard recent activity----
CREATE TABLE recent_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activity VARCHAR(255),
    status VARCHAR(50)
);

-- ----------------feedback----------------

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    sentiment ENUM('Positive','Negative','Suggestion') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- ----------------notifications----------------

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------users----------------

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NULL 
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- ALTER TABLE users ADD role ENUM('admin','user') NOT NULL DEFAULT 'user';
);