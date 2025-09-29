-- SMARTGRADE Database Initialization Script
-- This script sets up the initial database structure and sample data

-- Create Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'teacher',
    full_name TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Students table
CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id TEXT UNIQUE NOT NULL,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    grade_level INTEGER NOT NULL,
    section TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    subject_code TEXT UNIQUE NOT NULL,
    subject_name TEXT NOT NULL,
    grade_level INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create SF9 Forms table (Report Card)
CREATE TABLE IF NOT EXISTS sf9_forms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    subject_id INTEGER NOT NULL,
    quarter INTEGER NOT NULL,
    written_work REAL DEFAULT 0,
    performance_task REAL DEFAULT 0,
    quarterly_assessment REAL DEFAULT 0,
    final_grade REAL DEFAULT 0,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students (id),
    FOREIGN KEY (subject_id) REFERENCES subjects (id)
);

-- Create SF10 Forms table (Learner's Permanent Record)
CREATE TABLE IF NOT EXISTS sf10_forms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    school_year TEXT NOT NULL,
    grade_level INTEGER NOT NULL,
    section TEXT NOT NULL,
    general_average REAL DEFAULT 0,
    final_rating TEXT,
    action_taken TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students (id)
);

-- Create Academic Achievers table
CREATE TABLE IF NOT EXISTS academic_achievers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    achievement_type TEXT NOT NULL,
    school_year TEXT NOT NULL,
    quarter INTEGER,
    average_grade REAL NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students (id)
);

-- Insert default admin user
INSERT OR IGNORE INTO users (username, password_hash, role, full_name)
VALUES ('admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', 'System Administrator');

-- Insert sample subjects
INSERT OR IGNORE INTO subjects (subject_code, subject_name, grade_level) VALUES
('MATH7', 'Mathematics', 7),
('ENG7', 'English', 7),
('SCI7', 'Science', 7),
('FIL7', 'Filipino', 7),
('SS7', 'Social Studies', 7),
('MATH8', 'Mathematics', 8),
('ENG8', 'English', 8),
('SCI8', 'Science', 8),
('FIL8', 'Filipino', 8),
('SS8', 'Social Studies', 8),
('MATH9', 'Mathematics', 9),
('ENG9', 'English', 9),
('SCI9', 'Science', 9),
('FIL9', 'Filipino', 9),
('SS9', 'Social Studies', 9),
('MATH10', 'Mathematics', 10),
('ENG10', 'English', 10),
('SCI10', 'Science', 10),
('FIL10', 'Filipino', 10),
('SS10', 'Social Studies', 10);

-- Insert sample students
INSERT OR IGNORE INTO students (student_id, first_name, last_name, grade_level, section) VALUES
('2024-001', 'Juan', 'Dela Cruz', 7, 'Einstein'),
('2024-002', 'Maria', 'Santos', 7, 'Einstein'),
('2024-003', 'Jose', 'Rizal', 8, 'Newton'),
('2024-004', 'Ana', 'Garcia', 8, 'Newton'),
('2024-005', 'Pedro', 'Reyes', 9, 'Darwin'),
('2024-006', 'Rosa', 'Flores', 9, 'Darwin'),
('2024-007', 'Miguel', 'Torres', 10, 'Curie'),
('2024-008', 'Carmen', 'Lopez', 10, 'Curie');

-- Insert sample grades for demonstration
INSERT OR IGNORE INTO sf9_forms (student_id, subject_id, quarter, written_work, performance_task, quarterly_assessment, final_grade, remarks) VALUES
(1, 1, 2, 85.5, 88.0, 82.0, 86.1, 'Very Satisfactory'),
(1, 2, 2, 90.0, 92.5, 88.0, 90.8, 'Outstanding'),
(2, 1, 2, 78.0, 80.5, 75.0, 78.4, 'Fairly Satisfactory'),
(2, 2, 2, 82.0, 85.0, 80.0, 83.1, 'Satisfactory'),
(3, 6, 2, 95.0, 93.0, 96.0, 94.3, 'Outstanding'),
(3, 7, 2, 88.0, 90.0, 85.0, 88.1, 'Very Satisfactory');
