document.addEventListener('DOMContentLoaded', function() {
    console.log('[DEBUG] JS loaded');
    // Get modal element once
    // const addStudentModal = document.getElementById('addStudentModal');
    // const modal = addStudentModal ? new bootstrap.Modal(addStudentModal) : null;

    // Handle semester selection
    const semesterRadios = document.querySelectorAll('input[name="semester"]');
    semesterRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('semester', this.value);
            const activeSection = document.querySelector('.content-section.active');
            if (activeSection) {
                urlParams.set('section', activeSection.id);
            }
            window.location.href = 'teacher-dashboard.php?' + urlParams.toString();
        });
    });

    // Handle subject clicks
    // const subjectLinks = document.querySelectorAll('.subject-link');
    // subjectLinks.forEach(link => {
    //     link.addEventListener('click', function(e) {
    //         e.preventDefault();
    //         const subject = this.dataset.subject;
    //         document.getElementById('subjectTitle').textContent = subject;
    //         document.getElementById('subjects').style.display = 'none';
    //         document.getElementById('subjectStudents').style.display = 'block';
            
    //         // Load students for this subject
    //         loadStudents(subject);
    //     });
    // });

    // Search functionality
    // const searchInput = document.getElementById('searchInput');
    // if (searchInput) {
    //     searchInput.addEventListener('input', function() {
    //         const searchTerm = this.value.toLowerCase();
    //         const students = document.querySelectorAll('.student');
            
    //         students.forEach(student => {
    //             const text = student.textContent.toLowerCase();
    //             student.style.display = text.includes(searchTerm) ? 'block' : 'none';
    //         });
    //     });
    // }

    // Handle edit button clicks
    document.querySelectorAll('.edit-grades').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const gradeCells = row.querySelectorAll('.grade-cell');
            const studentId = row.dataset.studentId;
            
            // Toggle edit mode
            const isEditing = this.classList.contains('active');
            
            if (isEditing) {
                // Disable editing and save changes
                const grades = [];
                gradeCells.forEach(cell => {
                    cell.contentEditable = 'false';
                    cell.classList.remove('editing');
                    
                    const gradeId = cell.dataset.gradeId;
                    const grade = cell.textContent.trim();
                    const subjectId = cell.dataset.subjectId;

                    if (grade !== '-') {
                        grades.push({
                            grade_id: gradeId,
                            grade: grade,
                            subject_id: subjectId,
                            student_id: studentId
                        });
                    }
                });

                // Save changes to database
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
                        alert('Grades updated successfully');
                    } else {
                        alert('Error updating grades: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating grades');
                });

                this.classList.remove('active');
                this.textContent = 'Edit';
            } else {
                // Enable editing
                gradeCells.forEach(cell => {
                    cell.contentEditable = 'true';
                    cell.classList.add('editing');
                });
                this.classList.add('active');
                this.textContent = 'Done';
            }
        });
    });

    // Handle grade cell editing
    const gradeCells = document.querySelectorAll('.grade-cell');
    gradeCells.forEach(cell => {
        cell.addEventListener('blur', function() {
            const grade = this.textContent.trim();
            if (grade !== '-' && (isNaN(grade) || grade < 0 || grade > 100)) {
                alert('Grade must be between 0 and 100');
                this.textContent = this.getAttribute('data-original-value');
            }
        });

        cell.addEventListener('focus', function() {
            this.setAttribute('data-original-value', this.textContent);
        });
    });

    // Profile section functionality
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const formInputs = document.querySelectorAll('#profile input[type="text"], #profile input[type="email"], #profile input[type="password"]');

    if (editBtn && saveBtn) {
        editBtn.addEventListener('click', function() {
            formInputs.forEach(input => {
                input.disabled = false;
            });
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
        });

        saveBtn.addEventListener('click', function() {
            formInputs.forEach(input => {
                input.disabled = true;
            });
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
        });
    }

    // Sidebar navigation functionality
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');

    // Function to show a specific section
    function showSection(sectionId) {
        // Hide all sections
        contentSections.forEach(section => {
            section.style.display = 'none';
            section.classList.remove('active');
        });
        
        // Remove active class from all links
        navLinks.forEach(link => link.classList.remove('active'));
        
        // Show the target section and activate its link
        const targetSection = document.getElementById(sectionId);
        const targetLink = document.querySelector(`[data-section="${sectionId}"]`);
        
        if (targetSection) {
            targetSection.style.display = 'block';
            targetSection.classList.add('active');
        }
        
        if (targetLink) {
            targetLink.classList.add('active');
        }

        // Update URL with section parameter
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('section', sectionId);
        window.history.pushState({}, '', '?' + urlParams.toString());
    }

    // Handle navigation clicks using event delegation
    document.querySelector('.sidebar').addEventListener('click', function(e) {
        if (e.target.classList.contains('nav-link')) {
            e.preventDefault();
            const sectionId = e.target.getAttribute('data-section');
            if (sectionId) {
                showSection(sectionId);
            }
        }
    });

    // Check URL parameters on page load and show appropriate section
    const urlParams = new URLSearchParams(window.location.search);
    const sectionParam = urlParams.get('section');
    if (sectionParam) {
        showSection(sectionParam);
    } else {
        // Default to profile section if no section specified
        showSection('profile');
    }

    // Edit Grades functionality
    // const editSemesterRadios = document.querySelectorAll('input[name="editSemester"]');
    // editSemesterRadios.forEach(radio => {
    //     radio.addEventListener('change', function() {
    //         const urlParams = new URLSearchParams(window.location.search);
    //         urlParams.set('semester', this.value);
    //         urlParams.set('section', 'editGrades');
    //         window.location.href = 'teacher-dashboard.php?' + urlParams.toString();
    //     });
    // });

    // Handle subject selection
    // const subjectSelect = document.getElementById('subjectSelect');
    // if (subjectSelect) {
    //     subjectSelect.addEventListener('change', function() {
    //         if (this.value) {
    //             loadStudentsForEditing(this.value);
    //         }
    //     });
    // }

    // === Student Modal Add/Edit/Delete Logic (CLEAN IMPLEMENTATION) ===
    // Add Student
    // const addStudentBtn = document.getElementById('addStudentBtn');
    // if (addStudentBtn) {
    //     addStudentBtn.addEventListener('click', function() {
    //         document.getElementById('studentForm').reset();
    //         document.getElementById('studentModalTitle').textContent = 'Add New Student';
    //         document.getElementById('studentModalId').value = '';
    //         document.getElementById('studentModalIdInput').readOnly = false;
    //         document.getElementById('studentModalPassword').required = true;
    //         new bootstrap.Modal(document.getElementById('addStudentModal')).show();
    //     });
    // }

    // Add/Edit Student Submit
    // const studentForm = document.getElementById('studentForm');
    // if (studentForm) {
    //     studentForm.addEventListener('submit', function(e) {
    //         e.preventDefault();
    //         const isEdit = !!document.getElementById('studentModalId').value;
    //             const studentData = {
    //             action: isEdit ? 'edit' : 'add',
    //             studentIdInput: document.getElementById('studentModalIdInput').value.trim(),
    //             fullName: document.getElementById('studentModalFullName').value.trim(),
    //             sex: document.getElementById('studentModalSex').value,
    //             address: document.getElementById('studentModalAddress').value.trim(),
    //             contactNumber: document.getElementById('studentModalContactNumber').value.trim(),
    //             email: document.getElementById('studentModalEmail').value.trim(),
    //             password: document.getElementById('studentModalPassword').value
    //         };
    //         if (isEdit) {
    //             studentData.studentId = document.getElementById('studentModalId').value;
    //             if (!studentData.password) delete studentData.password;
    //         }
    //         fetch('student_operations.php', {
    //             method: 'POST',
    //             headers: { 'Content-Type': 'application/json' },  
    //             body: JSON.stringify(studentData)
    //         })
    //         .then(res => res.json())
    //         .then(data => {
    //             if (data.success) {
    //                 alert('Student ' + (isEdit ? 'updated' : 'added') + ' successfully');
    //                 bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
    //                 window.location.reload();
    //             } else {
    //                 alert(data.message || 'Failed to save student');
    //             }
    //         })
    //         .catch(err => {
    //             alert('Error: ' + err.message);
    //         });
    //     });
    // }

    // Edit Student
    // document.addEventListener('click', function(e) {
    //     if (e.target.classList.contains('edit-student')) {
    //         const studentId = e.target.dataset.studentId;
    //         fetch('student_operations.php', {
    //             method: 'POST',
    //             headers: { 'Content-Type': 'application/json' },
    //             body: JSON.stringify({ action: 'get', studentId })
    //         })
    //         .then(res => res.json())
    //         .then(data => {
    //             if (data.success) {
    //                 const s = data.student;
    //                 document.getElementById('studentModalTitle').textContent = 'Edit Student';
    //                 document.getElementById('studentModalId').value = s.student_id;
    //                 document.getElementById('studentModalIdInput').value = s.student_id;
    //                 document.getElementById('studentModalIdInput').readOnly = true;
    //                 document.getElementById('studentModalFullName').value = s.fullName;
    //                 document.getElementById('studentModalSex').value = s.sex;
    //                 document.getElementById('studentModalAddress').value = s.address;
    //                 document.getElementById('studentModalContactNumber').value = s.contactNumber;
    //                 document.getElementById('studentModalEmail').value = s.email;
    //                 document.getElementById('studentModalPassword').value = '';
    //                 document.getElementById('studentModalPassword').required = false;
    //                 new bootstrap.Modal(document.getElementById('addStudentModal')).show();
    //             } else {
    //                 alert('Error loading student: ' + data.message);
    //             }
    //         });
    //     }
    // });

    // Delete Student
    // document.addEventListener('click', function(e) {
    //     if (e.target.classList.contains('delete-student')) {
    //         if (confirm('Are you sure you want to delete this student?')) {
    //             const studentId = e.target.dataset.studentId;
    //             fetch('student_operations.php', {
    //                 method: 'POST',
    //                 headers: { 'Content-Type': 'application/json' },
    //                 body: JSON.stringify({ action: 'delete', studentId })
    //             })
    //             .then(res => res.json())
    //             .then(data => {
    //                 if (data.success) {
    //                     alert('Student deleted successfully!');
    //                     window.location.reload();
    //                 } else {
    //                     alert('Error deleting student: ' + data.message);
    //                 }
    //             })
    //             .catch(err => {
    //                 alert('Error: ' + err.message);
    //             });
    //         }
    //     }
    // });

    // Grade saving functionality (assuming this is still needed for teacher dashboard)
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
            fetch('../backend/grade_operations.php', {
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

    // Function to attach event listeners to buttons in Subjects section
    function attachSubjectsButtonListeners() {
         // Attach event listeners to dropdown items
         document.querySelectorAll('.dropdown-item.save-grades').forEach(item => {
             item.addEventListener('click', function(e) {
                 e.preventDefault(); // Prevent default link behavior
                 const studentId = this.getAttribute('data-student-id');
                 const row = this.closest('tr');
                 const gradeInputs = row.querySelectorAll('.grade-input');
                 const semester = document.querySelector('input[name="semester"]:checked').value;

                 const gradesToSave = Array.from(gradeInputs).map(input => ({
                     student_id: studentId,
                     subject_id: input.getAttribute('data-subject-id'),
                     grade: input.value,
                     semester: semester
                 })).filter(grade => grade.grade !== ''); // Only send grades that are not empty

                 console.log('Saving grades for student:', studentId);
                 console.log('Grades:', gradesToSave);

                 if (gradesToSave.length === 0) {
                     console.log('No grades entered to save for this student.');
                     // Optionally show a message to the user
                     return;
                 }

                 // Optionally show a saving indicator (e.g., on the dropdown item text or the main Actions button)
                 // e.target.textContent = 'Saving...';
                 // e.target.classList.add('disabled');

                 fetch('grade_operations.php', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                     },
                     body: JSON.stringify({
                         action: 'update',
                         grades: gradesToSave
                     })
                 })
                 .then(response => {
                     if (!response.ok) {
                         return response.text().then(text => {
                             throw new Error(`HTTP error! status: ${response.status}, Body: ${text}`);
                         });
                     }
                     return response.json();
                 })
                 .then(data => {
                     if (data.success) {
                         console.log('Grades saved successfully!');
                         // Show success message or update UI as needed
                         fetchAndDisplaySemesterData(semester); // Reload data to show updated GPA
                     } else {
                         console.error('Error saving grades:', data.message);
                         // Show error message
                     }
                 })
                 .catch(error => {
                     console.error('Fetch error during grade save:', error);
                     // Show error message
                 })
                 .finally(() => {
                     // Restore the dropdown item text/state
                     // e.target.textContent = 'Save';
                     // e.target.classList.remove('disabled');
                 });
             });
         });

         document.querySelectorAll('.dropdown-item.generate-report-card').forEach(item => {
             item.addEventListener('click', function(e) {
                 e.preventDefault();
                 const studentId = this.getAttribute('data-student-id');
                 const studentName = this.getAttribute('data-student-name');

                 // Assuming sf9_merged.html is in the 'school card' directory one level up from 'backend'
                 window.open(`../../school card/sf9_merged.html?student=${encodeURIComponent(JSON.stringify({ id: studentId, name: studentName }))}`, '_blank');
             });
         });

         document.querySelectorAll('.dropdown-item.generate-sf10').forEach(item => {
             item.addEventListener('click', function(e) {
                 e.preventDefault();
                 const studentId = this.getAttribute('data-student-id');
                 const studentName = this.getAttribute('data-student-name');

                 const studentData = encodeURIComponent(JSON.stringify({
                     id: studentId,
                     name: studentName
                 }));

                 window.open(`../../school card/sf10_merged.html?student=${studentData}`, '_blank');
             });
         });

         document.querySelectorAll('.dropdown-item.generate-certificate').forEach(item => {
             item.addEventListener('click', function(e) {
                 e.preventDefault();
                 const studentId = this.getAttribute('data-student-id');
                 const studentName = this.getAttribute('data-student-name');

                 window.open(`../../certificate/index.html?student=${encodeURIComponent(studentName)}`, '_blank');
             });
         });
     }

     // Call attachButtonListeners when the page loads
    attachSubjectsButtonListeners();

     // Also call attachButtonListeners after any dynamic content updates
     const observer = new MutationObserver(function(mutations) {
         mutations.forEach(function(mutation) {
             if (mutation.addedNodes.length) {
                 attachSubjectsButtonListeners();
             }
         });
     });

     // Start observing the document body for changes
     observer.observe(document.body, {
         childList: true,
         subtree: true
     });
});

// Removed unused functions related to student management and subjects:
// loadStudents
// goBackToSubjects
// loadStudentsForEditing
// deleteStudent
// updateGrade
