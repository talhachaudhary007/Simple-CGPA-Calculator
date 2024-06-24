CREATE DATABASE cgpa_calculator;

USE cgpa_calculator;

CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester INT NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    credit_hours INT NOT NULL,
    grade_points FLOAT NOT NULL
);
