// Dashboard-specific JavaScript functions

// Modal Functions
function openModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.style.display = "flex"
    document.body.style.overflow = "hidden"
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId)
  if (modal) {
    modal.style.display = "none"
    document.body.style.overflow = "auto"

    // Reset form if exists
    const form = modal.querySelector("form")
    if (form) {
      form.reset()
    }

    // Hide grade result
    const gradeResult = document.getElementById("gradeResult")
    if (gradeResult) {
      gradeResult.style.display = "none"
    }
  }
}

// Quick Action Functions
function openGradeModal() {
  loadStudentsForSelect()
  openModal("gradeModal")
}

function openStudentModal() {
  openModal("studentModal")
}

function generateSF9() {
  alert("SF9 Report generation feature coming soon!")
}

function generateSF10() {
  alert("SF10 Report generation feature coming soon!")
}

function viewAchievers() {
  alert("Academic Achievers view coming soon!")
}

// Grade Calculator Functions
async function calculateGrade() {
  const writtenWork = Number.parseFloat(document.getElementById("writtenWork").value) || 0
  const performanceTask = Number.parseFloat(document.getElementById("performanceTask").value) || 0
  const quarterlyAssessment = Number.parseFloat(document.getElementById("quarterlyAssessment").value) || 0

  if (writtenWork === 0 && performanceTask === 0 && quarterlyAssessment === 0) {
    alert("Please enter at least one grade component")
    return
  }

  try {
    const result = await window.SmartGrade.computeGrade(writtenWork, performanceTask, quarterlyAssessment)

    if (result.success) {
      const gradeResult = document.getElementById("gradeResult")
      const finalGradeValue = document.getElementById("finalGradeValue")
      const gradeRemarks = document.getElementById("gradeRemarks")

      finalGradeValue.textContent = result.final_grade.toFixed(2)
      gradeRemarks.textContent = result.remarks

      // Color code the grade
      const gradeValue = result.final_grade
      if (gradeValue >= 90) {
        finalGradeValue.style.color = "#059669" // Green
      } else if (gradeValue >= 85) {
        finalGradeValue.style.color = "#0891b2" // Blue
      } else if (gradeValue >= 80) {
        finalGradeValue.style.color = "#ea580c" // Orange
      } else if (gradeValue >= 75) {
        finalGradeValue.style.color = "#dc2626" // Red
      } else {
        finalGradeValue.style.color = "#991b1b" // Dark Red
      }

      gradeResult.style.display = "block"
    } else {
      alert("Error calculating grade: " + result.message)
    }
  } catch (error) {
    alert("Error calculating grade: " + error.message)
  }
}

// Student Management Functions
async function loadStudentsForSelect() {
  try {
    const students = await window.SmartGrade.loadStudents()
    const studentSelect = document.getElementById("studentSelect")

    // Clear existing options except the first one
    studentSelect.innerHTML = '<option value="">Select Student</option>'

    students.forEach((student) => {
      const option = document.createElement("option")
      option.value = student.id
      option.textContent = `${student.last_name}, ${student.first_name} (${student.student_id})`
      studentSelect.appendChild(option)
    })
  } catch (error) {
    console.error("Error loading students:", error)
    alert("Error loading students: " + error.message)
  }
}

// Form Handlers
async function handleStudentForm(event) {
  event.preventDefault()

  const formData = {
    student_id: document.getElementById("studentId").value.trim(),
    first_name: document.getElementById("firstName").value.trim(),
    last_name: document.getElementById("lastName").value.trim(),
    grade_level: Number.parseInt(document.getElementById("gradeLevel").value),
    section: document.getElementById("section").value.trim(),
  }

  try {
    const result = await window.SmartGrade.addStudent(formData)

    if (result.success) {
      alert("Student added successfully!")
      closeModal("studentModal")

      // Refresh dashboard stats
      window.loadDashboardStats()
    } else {
      alert("Error adding student: " + result.message)
    }
  } catch (error) {
    alert("Error adding student: " + error.message)
  }
}

async function handleGradeForm(event) {
  event.preventDefault()

  const studentId = document.getElementById("studentSelect").value
  const subjectId = document.getElementById("subjectSelect").value
  const quarter = document.getElementById("quarterSelect").value
  const writtenWork = Number.parseFloat(document.getElementById("modalWrittenWork").value)
  const performanceTask = Number.parseFloat(document.getElementById("modalPerformanceTask").value)
  const quarterlyAssessment = Number.parseFloat(document.getElementById("modalQuarterlyAssessment").value)

  if (!studentId || !subjectId) {
    alert("Please select both student and subject")
    return
  }

  const gradeData = {
    student_id: Number.parseInt(studentId),
    subject_id: Number.parseInt(subjectId),
    quarter: Number.parseInt(quarter),
    written_work: writtenWork,
    performance_task: performanceTask,
    quarterly_assessment: quarterlyAssessment,
  }

  try {
    const result = await window.SmartGrade.apiCall("/sf9", {
      method: "POST",
      body: JSON.stringify(gradeData),
    })

    if (result.success) {
      alert("Grade recorded successfully!")
      closeModal("gradeModal")

      // Refresh dashboard stats
      window.loadDashboardStats()
    } else {
      alert("Error recording grade: " + result.message)
    }
  } catch (error) {
    alert("Error recording grade: " + error.message)
  }
}

// Initialize Dashboard-specific functionality
document.addEventListener("DOMContentLoaded", () => {
  // Only run on dashboard page
  if (window.location.pathname.includes("dashboard.html")) {
    // Setup form handlers
    const studentForm = document.getElementById("studentForm")
    if (studentForm) {
      studentForm.addEventListener("submit", handleStudentForm)
    }

    const gradeForm = document.getElementById("gradeForm")
    if (gradeForm) {
      gradeForm.addEventListener("submit", handleGradeForm)
    }

    // Setup modal close on outside click
    const modals = document.querySelectorAll(".modal")
    modals.forEach((modal) => {
      modal.addEventListener("click", (event) => {
        if (event.target === modal) {
          closeModal(modal.id)
        }
      })
    })

    // Setup keyboard shortcuts
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        // Close any open modal
        const openModal = document.querySelector('.modal[style*="flex"]')
        if (openModal) {
          closeModal(openModal.id)
        }
      }
    })
  }
})
