-- Create the database
CREATE DATABASE IF NOT EXISTS php_complete_authentication;

-- Use the database
USE php_complete_authentication;

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    profilePicture VARCHAR(255) DEFAULT NULL
);
