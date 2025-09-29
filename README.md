# SMARTGRADE System

An automated grading system designed for Ampayon Senior National High School to manage SF9 and SF10 forms, compute grades automatically, identify academic achievers, and generate certificates.

## Features

- **Automated Grade Computation**: Uses DepEd grading formula (WW: 30%, PT: 50%, QA: 20%)
- **SF9 Form Management**: Record and manage quarterly grades
- **SF10 Form Management**: Track learner's permanent records
- **Academic Achievers Tracking**: Automatically identify high-performing students
- **User Authentication**: Secure login system for teachers and administrators
- **Dashboard Analytics**: Real-time statistics and recent activities
- **Grade Calculator**: Interactive tool for quick grade computations

## System Architecture

\`\`\`
SMARTGRADE-SYSTEM/
├── frontend/                # Client-side application
│   ├── index.html          # Login page
│   ├── dashboard.html      # Main dashboard
│   ├── styles.css          # Styling
│   ├── script.js           # Core JavaScript functions
│   └── dashboard.js        # Dashboard-specific functions
├── backend/                # Python Flask server
│   ├── app.py              # Flask application
│   ├── models.py           # Database models and logic
│   ├── requirements.txt    # Python dependencies
│   └── smartgrade.db       # SQLite database (auto-generated)
└── README.md
\`\`\`

## Installation & Setup

### Backend Setup

1. Navigate to the backend directory:
   \`\`\`bash
   cd backend
   \`\`\`

2. Install Python dependencies:
   \`\`\`bash
   pip install -r requirements.txt
   \`\`\`

3. Run the Flask server:
   \`\`\`bash
   python app.py
   \`\`\`

The backend server will start on `http://localhost:5000`

### Frontend Setup

1. Navigate to the frontend directory:
   \`\`\`bash
   cd frontend
   \`\`\`

2. Serve the files using a local web server:
   \`\`\`bash
   # Using Python 3
   python -m http.server 8000
   
   # Using Node.js (if you have http-server installed)
   npx http-server -p 8000
   \`\`\`

3. Open your browser and go to `http://localhost:8000`

## Default Login Credentials

- **Username**: admin
- **Password**: admin123

## Database Schema

### Users Table
- `id`: Primary key
- `username`: Unique username
- `password_hash`: Hashed password
- `role`: User role (admin/teacher)
- `full_name`: Full name of user

### Students Table
- `id`: Primary key
- `student_id`: Unique student identifier
- `first_name`: Student's first name
- `last_name`: Student's last name
- `grade_level`: Grade level (7-12)
- `section`: Class section

### SF9 Forms Table
- `id`: Primary key
- `student_id`: Foreign key to students
- `subject_id`: Foreign key to subjects
- `quarter`: Quarter number (1-4)
- `written_work`: Written work score
- `performance_task`: Performance task score
- `quarterly_assessment`: Quarterly assessment score
- `final_grade`: Computed final grade
- `remarks`: Grade remarks

### SF10 Forms Table
- `id`: Primary key
- `student_id`: Foreign key to students
- `school_year`: Academic year
- `grade_level`: Grade level
- `section`: Class section
- `general_average`: Overall average
- `final_rating`: Final rating
- `action_taken`: Promotion/retention status

### Academic Achievers Table
- `id`: Primary key
- `student_id`: Foreign key to students
- `achievement_type`: Type of achievement
- `school_year`: Academic year
- `quarter`: Quarter (if applicable)
- `average_grade`: Average grade achieved

## Grade Computation Formula

The system uses the official DepEd grading formula:

**Final Grade = (Written Work × 30%) + (Performance Task × 50%) + (Quarterly Assessment × 20%)**

### Grade Remarks
- 90-100: Outstanding
- 85-89: Very Satisfactory
- 80-84: Satisfactory
- 75-79: Fairly Satisfactory
- Below 75: Did Not Meet Expectations

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout

### Students
- `GET /api/students` - Get all students
- `POST /api/students` - Add new student

### Grades
- `POST /api/grades/compute` - Compute final grade
- `POST /api/sf9` - Save SF9 form data

### Dashboard
- `GET /api/dashboard/stats` - Get dashboard statistics

## Features Overview

### Login System
- Secure authentication with hashed passwords
- Session management
- Role-based access control

### Dashboard
- Real-time statistics display
- Recent activities tracking
- Quick action buttons for common tasks

### Grade Management
- Interactive grade calculator
- SF9 form recording
- Automatic grade computation
- Grade remarks generation

### Student Management
- Add new students
- View student lists
- Track student performance

## Development Notes

- Frontend uses vanilla HTML, CSS, and JavaScript
- Backend uses Python Flask with SQLite database
- CORS enabled for cross-origin requests
- Session-based authentication
- Responsive design for mobile compatibility

## Future Enhancements

- Certificate generation
- Report printing functionality
- Data export capabilities
- Advanced analytics
- Email notifications
- Bulk data import
- Academic calendar integration

## Support

For technical support or questions about the SMARTGRADE system, please contact the system administrator.

---

**SMARTGRADE System** - Automating Excellence in Education
*Ampayon Senior National High School*
