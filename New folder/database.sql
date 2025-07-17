-- Create the students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(50) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    sex ENUM('Male', 'Female') NOT NULL,
    address TEXT,
    contact_number VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create the grades table
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    semester ENUM('1st', '2nd') NOT NULL,
    grade DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    UNIQUE KEY unique_grade (student_id, subject, semester)
);

-- Add sample students with login credentials
INSERT INTO students (student_id, username, password, full_name, sex, address, contact_number, email) VALUES
('231-01423', 'john.doe', 'password123', 'John Doe', 'Male', '123 Main St, City', '09123456789', 'john.doe@email.com'),
('231-01424', 'jane.smith', 'password123', 'Jane Smith', 'Female', '456 Oak St, Town', '09234567890', 'jane.smith@email.com'),
('231-01425', 'mike.johnson', 'password123', 'Mike Johnson', 'Male', '789 Pine St, Village', '09345678901', 'mike.johnson@email.com'),
('231-01426', 'sarah.williams', 'password123', 'Sarah Williams', 'Female', '321 Elm St, Borough', '09456789012', 'sarah.williams@email.com');

-- Add sample grades
INSERT INTO grades (student_id, subject, semester, grade) VALUES
('231-01423', 'English', '1st', 85.5),
('231-01423', 'Mathematics', '1st', 90.0),
('231-01424', 'English', '1st', 88.0),
('231-01424', 'Mathematics', '1st', 92.5),
('231-01425', 'English', '1st', 87.0),
('231-01425', 'Mathematics', '1st', 89.5),
('231-01426', 'English', '1st', 91.0),
('231-01426', 'Mathematics', '1st', 88.5); 