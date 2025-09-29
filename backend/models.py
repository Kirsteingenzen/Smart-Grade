from datetime import datetime
import sqlite3
import hashlib

class Database:
    def __init__(self, db_path='smartgrade.db'):
        self.db_path = db_path
        self.init_database()
    
    def init_database(self):
        """Initialize database with required tables"""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        # Users table for authentication
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'teacher',
                full_name TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Students table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS students (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id TEXT UNIQUE NOT NULL,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                grade_level INTEGER NOT NULL,
                section TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Subjects table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS subjects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                subject_code TEXT UNIQUE NOT NULL,
                subject_name TEXT NOT NULL,
                grade_level INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # SF9 Forms (Report Card)
        cursor.execute('''
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
            )
        ''')
        
        # SF10 Forms (Learner's Permanent Record)
        cursor.execute('''
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
            )
        ''')
        
        # Academic achievers tracking
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS academic_achievers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER NOT NULL,
                achievement_type TEXT NOT NULL,
                school_year TEXT NOT NULL,
                quarter INTEGER,
                average_grade REAL NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES students (id)
            )
        ''')
        
        conn.commit()
        conn.close()
        
        # Create default admin user
        self.create_default_admin()
    
    def create_default_admin(self):
        """Create default admin user if not exists"""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute('SELECT COUNT(*) FROM users WHERE role = "admin"')
        admin_count = cursor.fetchone()[0]
        
        if admin_count == 0:
            password_hash = hashlib.sha256('admin123'.encode()).hexdigest()
            cursor.execute('''
                INSERT INTO users (username, password_hash, role, full_name)
                VALUES (?, ?, ?, ?)
            ''', ('admin', password_hash, 'admin', 'System Administrator'))
            conn.commit()
        
        conn.close()
    
    def get_connection(self):
        """Get database connection"""
        return sqlite3.connect(self.db_path)

class User:
    @staticmethod
    def authenticate(username, password):
        """Authenticate user login"""
        db = Database()
        conn = db.get_connection()
        cursor = conn.cursor()
        
        password_hash = hashlib.sha256(password.encode()).hexdigest()
        cursor.execute('''
            SELECT id, username, role, full_name FROM users 
            WHERE username = ? AND password_hash = ?
        ''', (username, password_hash))
        
        user = cursor.fetchone()
        conn.close()
        
        if user:
            return {
                'id': user[0],
                'username': user[1],
                'role': user[2],
                'full_name': user[3]
            }
        return None

class Student:
    @staticmethod
    def get_all():
        """Get all students"""
        db = Database()
        conn = db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute('''
            SELECT id, student_id, first_name, last_name, grade_level, section
            FROM students ORDER BY last_name, first_name
        ''')
        
        students = cursor.fetchall()
        conn.close()
        
        return [
            {
                'id': s[0],
                'student_id': s[1],
                'first_name': s[2],
                'last_name': s[3],
                'grade_level': s[4],
                'section': s[5]
            }
            for s in students
        ]
    
    @staticmethod
    def add(student_id, first_name, last_name, grade_level, section):
        """Add new student"""
        db = Database()
        conn = db.get_connection()
        cursor = conn.cursor()
        
        try:
            cursor.execute('''
                INSERT INTO students (student_id, first_name, last_name, grade_level, section)
                VALUES (?, ?, ?, ?, ?)
            ''', (student_id, first_name, last_name, grade_level, section))
            conn.commit()
            return True
        except sqlite3.IntegrityError:
            return False
        finally:
            conn.close()

class Grade:
    @staticmethod
    def compute_final_grade(written_work, performance_task, quarterly_assessment):
        """Compute final grade based on DepEd formula"""
        # DepEd grading system: WW=30%, PT=50%, QA=20%
        final_grade = (written_work * 0.30) + (performance_task * 0.50) + (quarterly_assessment * 0.20)
        return round(final_grade, 2)
    
    @staticmethod
    def get_grade_remarks(grade):
        """Get grade remarks based on DepEd standards"""
        if grade >= 90:
            return "Outstanding"
        elif grade >= 85:
            return "Very Satisfactory"
        elif grade >= 80:
            return "Satisfactory"
        elif grade >= 75:
            return "Fairly Satisfactory"
        else:
            return "Did Not Meet Expectations"
