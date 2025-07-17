DROP TABLE IF EXISTS teachers;
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    sex ENUM('Male', 'Female') NOT NULL,
    email VARCHAR(255),
    contact_number VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS students;
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(255),
    email VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS subjects;
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS grades;
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    subject_id INT,
    grade DECIMAL(5,2) NOT NULL,
    semester ENUM('1st', '2nd') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_grade (student_id, subject_id)
);

-- Insert sample teachers
INSERT INTO teachers (username, password, full_name, sex, email, contact_number, address) VALUES
('teacher1', 'password123', 'John Smith', 'Male', 'john.smith@school.com', '09123456789', '789 Teacher St, City'),
('teacher2', 'password123', 'Mary Johnson', 'Female', 'mary.johnson@school.com', '09234567890', '321 Faculty Ave, Town');

-- Insert sample subjects
INSERT INTO subjects (subject_name) VALUES
('Mathematics'),
('English'),
('Science'),
('History'),
('Physical Education');

-- Insert sample students
INSERT INTO students (student_id, full_name, gender, password, contact_number, email, address) VALUES
('231-01423', 'John Doe', 'Male', 'password123', '09345678901', 'john.doe@student.com', '123 Main St'),
('231-01424', 'Jane Smith', 'Female', 'password123', '09456789012', 'jane.smith@student.com', '456 Oak St');

-- Insert sample grades
INSERT INTO grades (student_id, subject_id, grade, semester) VALUES
('231-01423', 1, 85.5, '1st'),
('231-01423', 2, 90.0, '1st'),
('231-01424', 1, 88.0, '1st'),
('231-01424', 2, 92.5, '1st');