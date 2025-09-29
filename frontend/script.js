// API Configuration
const API_BASE_URL = "http://localhost:5000/api"

// Utility Functions
function showError(message) {
  const errorDiv = document.getElementById("errorMessage")
  if (errorDiv) {
    errorDiv.textContent = message
    errorDiv.style.display = "block"
  }
}

function hideError() {
  const errorDiv = document.getElementById("errorMessage")
  if (errorDiv) {
    errorDiv.style.display = "none"
  }
}

function showLoading(button) {
  const btnText = button.querySelector(".btn-text")
  const spinner = button.querySelector(".loading-spinner")

  btnText.style.display = "none"
  spinner.style.display = "block"
  button.disabled = true
}

function hideLoading(button) {
  const btnText = button.querySelector(".btn-text")
  const spinner = button.querySelector(".loading-spinner")

  btnText.style.display = "block"
  spinner.style.display = "none"
  button.disabled = false
}

// API Functions
async function apiCall(endpoint, options = {}) {
  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    })

    const data = await response.json()
    return data
  } catch (error) {
    console.error("API call failed:", error)
    throw new Error("Network error. Please check your connection.")
  }
}

// Login Functions
async function handleLogin(event) {
  event.preventDefault()

  const username = document.getElementById("username").value.trim()
  const password = document.getElementById("password").value
  const loginBtn = document.getElementById("loginBtn")

  if (!username || !password) {
    showError("Please enter both username and password")
    return
  }

  hideError()
  showLoading(loginBtn)

  try {
    const result = await apiCall("/login", {
      method: "POST",
      body: JSON.stringify({ username, password }),
    })

    if (result.success) {
      // Store user info in localStorage
      localStorage.setItem("user", JSON.stringify(result.user))

      // Redirect to dashboard
      window.location.href = "dashboard.html"
    } else {
      showError(result.message || "Login failed")
    }
  } catch (error) {
    showError(error.message)
  } finally {
    hideLoading(loginBtn)
  }
}

// Dashboard Functions
async function loadDashboardStats() {
  try {
    const result = await apiCall("/dashboard/stats")

    if (result.success) {
      updateStatsDisplay(result.stats)
    } else {
      console.error("Failed to load dashboard stats:", result.message)
    }
  } catch (error) {
    console.error("Error loading dashboard stats:", error)
  }
}

function updateStatsDisplay(stats) {
  // Update stat cards
  const totalStudentsEl = document.getElementById("totalStudents")
  const totalGradesEl = document.getElementById("totalGrades")
  const totalAchieversEl = document.getElementById("totalAchievers")

  if (totalStudentsEl) totalStudentsEl.textContent = stats.total_students
  if (totalGradesEl) totalGradesEl.textContent = stats.total_grades
  if (totalAchieversEl) totalAchieversEl.textContent = stats.total_achievers

  // Update recent activities
  const activitiesContainer = document.getElementById("recentActivities")
  if (activitiesContainer && stats.recent_activities) {
    activitiesContainer.innerHTML = ""

    if (stats.recent_activities.length === 0) {
      activitiesContainer.innerHTML = '<p class="text-gray-500">No recent activities</p>'
    } else {
      stats.recent_activities.forEach((activity) => {
        const activityEl = document.createElement("div")
        activityEl.className = "activity-item"
        activityEl.innerHTML = `
                    <div class="activity-info">
                        <div class="activity-student">${activity.student_name}</div>
                        <div class="activity-date">${new Date(activity.date).toLocaleDateString()}</div>
                    </div>
                    <div class="activity-grade">${activity.grade}</div>
                `
        activitiesContainer.appendChild(activityEl)
      })
    }
  }
}

async function handleLogout() {
  try {
    await apiCall("/logout", { method: "POST" })
    localStorage.removeItem("user")
    window.location.href = "index.html"
  } catch (error) {
    console.error("Logout error:", error)
    // Force logout even if API call fails
    localStorage.removeItem("user")
    window.location.href = "index.html"
  }
}

// Grade Computation Functions
async function computeGrade(writtenWork, performanceTask, quarterlyAssessment) {
  try {
    const result = await apiCall("/grades/compute", {
      method: "POST",
      body: JSON.stringify({
        written_work: writtenWork,
        performance_task: performanceTask,
        quarterly_assessment: quarterlyAssessment,
      }),
    })

    return result
  } catch (error) {
    console.error("Grade computation error:", error)
    throw error
  }
}

// Student Management Functions
async function loadStudents() {
  try {
    const result = await apiCall("/students")

    if (result.success) {
      return result.students
    } else {
      throw new Error(result.message || "Failed to load students")
    }
  } catch (error) {
    console.error("Error loading students:", error)
    throw error
  }
}

async function addStudent(studentData) {
  try {
    const result = await apiCall("/students", {
      method: "POST",
      body: JSON.stringify(studentData),
    })

    return result
  } catch (error) {
    console.error("Error adding student:", error)
    throw error
  }
}

// Authentication Check
function checkAuthentication() {
  const user = localStorage.getItem("user")
  const currentPage = window.location.pathname.split("/").pop()

  if (!user && currentPage === "dashboard.html") {
    window.location.href = "index.html"
    return false
  }

  if (user && currentPage === "index.html") {
    window.location.href = "dashboard.html"
    return false
  }

  return true
}

// Initialize Page
document.addEventListener("DOMContentLoaded", () => {
  // Check authentication
  if (!checkAuthentication()) {
    return
  }

  const currentPage = window.location.pathname.split("/").pop()

  if (currentPage === "index.html" || currentPage === "") {
    // Login page initialization
    const loginForm = document.getElementById("loginForm")
    if (loginForm) {
      loginForm.addEventListener("submit", handleLogin)
    }
  } else if (currentPage === "dashboard.html") {
    // Dashboard page initialization
    const user = JSON.parse(localStorage.getItem("user"))

    // Update user info display
    const userNameEl = document.getElementById("userName")
    if (userNameEl && user) {
      userNameEl.textContent = user.full_name
    }

    // Load dashboard data
    loadDashboardStats()

    // Setup logout button
    const logoutBtn = document.getElementById("logoutBtn")
    if (logoutBtn) {
      logoutBtn.addEventListener("click", handleLogout)
    }
  }
})

// Export functions for use in other scripts
window.SmartGrade = {
  apiCall,
  computeGrade,
  loadStudents,
  addStudent,
  handleLogout,
}
