CREATE DATABASE IF NOT EXISTS my_portal;
USE my_portal;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(100),
  role ENUM('teacher','student','admin')
);

CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_name VARCHAR(100),
  teacher_id INT
);

-- Add other tables if needed