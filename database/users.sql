CREATE DATABASE IF NOT EXISTS mydb;
USE mydb;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','moderator','user') NOT NULL DEFAULT 'user',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL
);

INSERT INTO users (username, email, password, role, is_active, created_at)
VALUES
    ('juandelacruz', 'juan@example.com', '$2y$10$8Jr8Y3mGkP5JbA0aBvKjE.7d2q0K3FhKJ0i4Yz0lK7HjvVqTR0XG', 'user', 1, NOW()),
    ('mariasantos', 'maria@example.com', '$2y$10$Qm8U5k4g8d3j6t7n8mQnVea7NQb2V5r5b7z8S9mP3F9kVjL6E1C2', 'user', 1, NOW()),
    ('pedrogarcia', 'pedro@example.com', '$2y$10$w1sGq5K4dG8hTn0D3b0lBefbJ/06y2H1dIM6mM77g8l7m4M7P1W6a', 'user', 1, NOW()),
    ('anareyes', 'ana@example.com', '$2y$10$H4qW0vP7Q5M1nL6R3jF9JexVvKTuJ4k19z1xYd1ZtJHbS1hQm6r5S', 'user', 1, NOW()),
    ('josemendoza', 'jose@example.com', '$2y$10$9n0f6d5QXrA8z7a0m3v2rUBVE7Tg7l7P9nYQjKOyG3xvV7H5dS1R', 'user', 1, NOW());