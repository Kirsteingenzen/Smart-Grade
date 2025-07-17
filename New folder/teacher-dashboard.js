document.addEventListener('DOMContentLoaded', function() {
    // Sidebar navigation
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            
            // Update active states
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Show target section
            contentSections.forEach(section => {
                section.classList.remove('active');
                if (section.id === targetSection) {
                    section.classList.add('active');
                }
            });
        });
    });

    // Grade saving functionality
    const saveButtons = document.querySelectorAll('.save-grades');
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const row = this.closest('tr');
            const gradeInputs = row.querySelectorAll('.grade-input');
            const semester = document.querySelector('input[name="semester"]:checked').value;
            
            const grades = Array.from(gradeInputs).map(input => ({
                student_id: studentId,
                subject_id: input.getAttribute('data-subject-id'),
                grade: input.value,
                semester: semester
            }));

            // Send grades to server
            fetch('backend/grade_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update',
                    grades: grades
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grades saved successfully!');
                } else {
                    alert('Error saving grades: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving grades. Please try again.');
            });
        });
    });

    // Profile form submission
    const profileForm = document.getElementById('teacherProfileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                action: 'update',
                full_name: formData.get('fullName'),
                sex: formData.get('sex'),
                address: formData.get('address'),
                contact_number: formData.get('contact'),
                email: formData.get('email')
            };

            if (formData.get('password')) {
                data.password = formData.get('password');
            }

            fetch('backend/teacher_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating profile: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating profile. Please try again.');
            });
        });
    }

    // Student Management
    const studentForm = document.getElementById('studentForm');
    if (studentForm) {
        studentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                action: formData.get('action'),
                student_id: formData.get('student_id'),
                full_name: formData.get('full_name'),
                gender: formData.get('gender'),
                password: formData.get('password'),
                contact_number: formData.get('contact_number'),
                email: formData.get('email'),
                address: formData.get('address')
            };

            fetch('backend/student_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Student ' + (formData.get('action') === 'add' ? 'added' : 'updated') + ' successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error. Please try again.');
            });
        });
    }

    // Delete student
    const deleteButtons = document.querySelectorAll('.delete-student');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this student?')) {
                const studentId = this.getAttribute('data-student-id');
                
                fetch('backend/student_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        student_id: studentId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Student deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting student: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting student. Please try again.');
                });
            }
        });
    });

    // Edit student
    const editButtons = document.querySelectorAll('.edit-student');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            
            fetch('backend/student_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get',
                    student_id: studentId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const student = data.student;
                    const form = document.getElementById('studentForm');
                    form.querySelector('[name="action"]').value = 'edit';
                    form.querySelector('[name="student_id"]').value = student.student_id;
                    form.querySelector('[name="full_name"]').value = student.full_name;
                    form.querySelector('[name="gender"]').value = student.gender;
                    form.querySelector('[name="contact_number"]').value = student.contact_number;
                    form.querySelector('[name="email"]').value = student.email;
                    form.querySelector('[name="address"]').value = student.address;
                    form.querySelector('[name="password"]').value = '';
                    form.querySelector('[name="password"]').placeholder = 'Leave blank to keep unchanged';
                    
                    const modal = new bootstrap.Modal(document.getElementById('studentModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error. Please try again.');
            });
        });
    });
}); 