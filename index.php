CREATE DATABASE flyer_development;

USE flyer_development;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('designer', 'proof_reader', 'management') NOT NULL
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status ENUM('design_pending', 'design_approved', 'draft_review', 'final_review', 'completed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE flyer_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    catalogue_name VARCHAR(100) NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    bulk_price DECIMAL(10, 2) NOT NULL,
    current_sp DECIMAL(10, 2) NOT NULL,
    promo_sp DECIMAL(10, 2) NOT NULL,
    page_number INT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE designs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    file_path VARCHAR(255) NOT NULL,
    version INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE drafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT,
    file_path VARCHAR(255) NOT NULL,
    version INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    project_id INT,
    design_id INT NULL,
    draft_id INT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (design_id) REFERENCES designs(id),
    FOREIGN KEY (draft_id) REFERENCES drafts(id)
);

CREATE TABLE approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    project_id INT,
    design_id INT NULL,
    draft_id INT NULL,
    status ENUM('approved', 'rejected') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (design_id) REFERENCES designs(id),
    FOREIGN KEY (draft_id) REFERENCES drafts(id)
);