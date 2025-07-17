<?php
session_start();
include "backend/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login_teacher'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $sql = "SELECT * FROM teachers WHERE username='$user' AND password='$pass'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $_SESSION['username'] = $user;
            header("Location: backend/teacher-dashboard.php");
            exit();
        } else {
            $error = "Invalid teacher credentials";
        }
    }

    if (isset($_POST['login_student'])) {
        $student_id = $_POST['student_id'];
        $pass = $_POST['student_password'];
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? AND password = ?");
        $stmt->bind_param("ss", $student_id, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
            $_SESSION['student_id'] = $student_id;
            $_SESSION['student_name'] = $student['full_name'];
            header("Location: backend/student-dashboard.php");
            exit();
        } else {
            $error = "Invalid student ID or password";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>SmartGrade</title>
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="#page-top">
                    <img src="assets/img/badge.png" alt="SmartGrade Logo" class="img-fluid" style="max-height: 30px; margin-right: 10px;">
                    SmartGrade
                </a>               
            </div>
        </nav>
        <!-- Masthead-->
        <header class="masthead">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold">SMARTGRADE: An Automated Grading and Records Management System</h1>
                        <hr class="divider" />
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <p class="text-white-75 mb-5">streamlines grade computation, achiever identification, and printing of SF9, SF10, and certificates.</p>
                        <a class="btn btn-primary btn-xl" href="#login">Continue to Login</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- LOGIN POPUP MODAL -->
        <div id="loginPopup" class="popup-overlay d-none">
            <div class="popup-content text-center p-5 rounded">    
                <!-- Logo + SmartGrade -->
                <div class="d-flex align-items-center mb-4 justify-content-between" style="position: absolute; top: 20px; left: 20px; right: 20px;">
                    <div class="d-flex align-items-center">
                        <img src="assets/img/black-badge.png" alt="SmartGrade Logo" class="img-fluid" style="max-height: 30px; margin-right: 10px;">
                        <h6 class="mb-0" style="font-size: 1rem;">SmartGrade</h6>
                    </div>
                    <button id="closePopup" class="btn-close" aria-label="Close"></button>
                </div>                               
                <!-- teacher or student section -->
                <div id="roleSelection" class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 mt-5">
                    <a href="#" id="teacherLoginBtn" class="role-box d-flex justify-content-center align-items-center">TEACHER</a>
                    <a href="#" id="studentLoginBtn" class="role-box d-flex justify-content-center align-items-center">STUDENT</a>
                </div> 
                <!-- teacher login form -->
                <div id="teacherLoginForm" class="mt-4 d-none">
                    <form method="post" class="mx-auto w-100" style="max-width: 400px;">
                        <?php if (!empty($error)) echo "<div class='text-danger mb-3'>$error</div>"; ?>
                        <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                        <input type="password" name="password" class="form-control mb-1" placeholder="Password" required>
                        <input type="submit" name="login_teacher" value="LOGIN" class="btn btn-primary w-50 mt-4" style="background-color: #f15a29;">
                    </form>
                </div>

                <!-- Student login form -->
                <div id="studentLoginForm" class="mt-4 d-none">
                    <form method="post" class="mx-auto w-100" style="max-width: 400px;">
                        <?php if (!empty($error)) echo "<div class='text-danger mb-3'>$error</div>"; ?>
                        <input type="text" name="student_id" class="form-control mb-3" placeholder="Student ID (e.g., 201-12345)" required>
                        <input type="password" name="student_password" class="form-control mb-1" placeholder="Password" required>
                        <input type="submit" name="login_student" value="LOGIN" class="btn btn-primary w-50 mt-4" style="background-color: #f15a29;">
                    </form>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SimpleLightbox plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <!-- SB Forms JS-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>



