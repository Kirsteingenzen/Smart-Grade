from flask import Flask, request, jsonify, session
from flask_cors import CORS
from models import Database, User, Student, Grade
import sqlite3
import hashlib
from datetime import datetime

app = Flask(__name__)
app.secret_key = 'smartgrade_secret_key_2024'
CORS(app, supports_credentials=True)

# Initialize database
db = Database()

@app.route('/api/login', methods=['POST'])
def login():
    """User authentication endpoint"""
    data = request.get_json()
    username = data.get('username')
    password = data.get('password')
    
    if not username or not password:
        return jsonify({'success': False, 'message': 'Username and password required'}), 400
    
    user = User.authenticate(username, password)
    if user:
        session['user_id'] = user['id']
        session['username'] = user['username']
        session['role'] = user['role']
        return jsonify({
            'success': True,
            'user': user,
            'message': 'Login successful'
        })
    else:
        return jsonify({'success': False, 'message': 'Invalid credentials'}), 401

@app.route('/api/logout', methods=['POST'])
def logout():
    """User logout endpoint"""
    session.clear()
    return jsonify({'success': True, 'message': 'Logged out successfully'})

@app.route('/api/students', methods=['GET'])
def get_students():
    """Get all students"""
    if 'user_id' not in session:
        return jsonify({'success': False, 'message': 'Not authenticated'}), 401
    
    students = Student.get_all()
    return jsonify({'success': True, 'students': students})

@app.route('/api/students', methods=['POST'])
def add_student():
    """Add new student"""
    if 'user_id' not in session:
        return jsonify({'success': False, 'message': 'Not authenticated'}), 401
    
    data = request.get_json()
    required_fields = ['student_id', 'first_name', 'last_name', 'grade_level', 'section']
    
    if not all(field in data for field in required_fields):
        return jsonify({'success': False, 'message': 'All fields required'}), 400
    
    success = Student.add(
        data['student_id'],
        data['first_name'],
        data['last_name'],
        data['grade_level'],
        data['section']
    )
    
    if success:
        return jsonify({'success': True, 'message': 'Student added successfully'})
    else:
        return jsonify({'success': False, 'message': 'Student ID already exists'}), 400

@app.route('/api/grades/compute', methods=['POST'])
def compute_grade():
    """Compute final grade"""
    data = request.get_json()
    
    try:
        written_work = float(data.get('written_work', 0))
        performance_task = float(data.get('performance_task', 0))
        quarterly_assessment = float(data.get('quarterly_assessment', 0))
        
        final_grade = Grade.compute_final_grade(written_work, performance_task, quarterly_assessment)
        remarks = Grade.get_grade_remarks(final_grade)
        
        return jsonify({
            'success': True,
            'final_grade': final_grade,
            'remarks': remarks
        })
    except (ValueError, TypeError):
        return jsonify({'success': False, 'message': 'Invalid grade values'}), 400

@app.route('/api/sf9', methods=['POST'])
def save_sf9():
    """Save SF9 form data"""
    if 'user_id' not in session:
        return jsonify({'success': False, 'message': 'Not authenticated'}), 401
    
    data = request.get_json()
    conn = db.get_connection()
    cursor = conn.cursor()
    
    try:
        final_grade = Grade.compute_final_grade(
            float(data['written_work']),
            float(data['performance_task']),
            float(data['quarterly_assessment'])
        )
        remarks = Grade.get_grade_remarks(final_grade)
        
        cursor.execute('''
            INSERT INTO sf9_forms 
            (student_id, subject_id, quarter, written_work, performance_task, 
             quarterly_assessment, final_grade, remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            data['student_id'], data['subject_id'], data['quarter'],
            data['written_work'], data['performance_task'], 
            data['quarterly_assessment'], final_grade, remarks
        ))
        
        conn.commit()
        return jsonify({'success': True, 'message': 'SF9 form saved successfully'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500
    finally:
        conn.close()

@app.route('/api/dashboard/stats', methods=['GET'])
def get_dashboard_stats():
    """Get dashboard statistics"""
    if 'user_id' not in session:
        return jsonify({'success': False, 'message': 'Not authenticated'}), 401
    
    conn = db.get_connection()
    cursor = conn.cursor()
    
    # Get total students
    cursor.execute('SELECT COUNT(*) FROM students')
    total_students = cursor.fetchone()[0]
    
    # Get total grades recorded
    cursor.execute('SELECT COUNT(*) FROM sf9_forms')
    total_grades = cursor.fetchone()[0]
    
    # Get academic achievers count
    cursor.execute('SELECT COUNT(*) FROM academic_achievers')
    total_achievers = cursor.fetchone()[0]
    
    # Get recent activities (last 10 grade entries)
    cursor.execute('''
        SELECT s.first_name, s.last_name, sf.final_grade, sf.created_at
        FROM sf9_forms sf
        JOIN students s ON sf.student_id = s.id
        ORDER BY sf.created_at DESC
        LIMIT 10
    ''')
    recent_activities = cursor.fetchall()
    
    conn.close()
    
    return jsonify({
        'success': True,
        'stats': {
            'total_students': total_students,
            'total_grades': total_grades,
            'total_achievers': total_achievers,
            'recent_activities': [
                {
                    'student_name': f"{activity[0]} {activity[1]}",
                    'grade': activity[2],
                    'date': activity[3]
                }
                for activity in recent_activities
            ]
        }
    })

if __name__ == '__main__':
    app.run(debug=True, port=5000)
