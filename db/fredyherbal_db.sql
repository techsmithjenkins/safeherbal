-- Database: fredyherbal_db
-- This SQL script creates the database and tables for the Fredy Herbal website.
-- It includes tables for admins, articles (including testimonials), and gallery.

-- Create the database
CREATE DATABASE IF NOT EXISTS fredyherbal_db;
USE fredyherbal_db;

-- Table: admins
-- Stores admin login details with secure password hashing.
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Stores password_hash()
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: articles
-- Stores articles and testimonials with an approval status for moderation.
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255), -- Path to uploaded image
    date_added DATE NOT NULL,
    tags VARCHAR(255), -- Comma-separated tags (e.g., testimonial)
    author VARCHAR(255) NOT NULL,
    is_approved TINYINT(1) DEFAULT 0 COMMENT '0 = Pending, 1 = Approved' -- New column for approval
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: gallery
-- Stores gallery images with captions.
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL, -- Path to uploaded image
    caption VARCHAR(255),
    date_added DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT NOT NULL,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table: treatments
-- Stores available treatment services with their details and featured images.
-- This table manages the various healing treatments and services offered by Fredy Herbal.
CREATE TABLE treatments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    caption TEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: testimonials
-- Stores customer testimonials with optional photos and approval status for moderation.

CREATE TABLE IF NOT EXISTS testimonials (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    topic VARCHAR(255) NOT NULL COLLATE utf8mb4_general_ci,
    message TEXT NOT NULL COLLATE utf8mb4_general_ci,
    photo LONGBLOB,
    photo_mime VARCHAR(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    is_approved TINYINT(1) DEFAULT 0,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: content_pages
-- Stores dynamic content for various pages and sections of the website.

CREATE TABLE content_pages (
    id INT(11) NOT NULL AUTO_INCREMENT,
    page VARCHAR(50) NOT NULL,
    section VARCHAR(100) NOT NULL,
    caption VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_page (page),
    INDEX idx_section (section)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

