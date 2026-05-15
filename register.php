<?php
require_once 'includes/config.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $studentId = trim($_POST['student_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($firstName) || empty($lastName) || empty($email) || empty($studentId) || empty($password)) {
        $error = 'Fill all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Valid email required';
    } elseif (strlen($password) < 6) {
        $error = 'Password min 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR student_id = ?");
        $stmt->execute([$email, $studentId]);
        if ($stmt->fetch()) {
            $error = 'Email or Student ID exists';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, student_id, password, role) VALUES (?,?,?,?,?,'student')");
            if ($stmt->execute([$firstName, $lastName, $email, $studentId, $hashedPassword])) {
                $success = 'Registration successful! Please login.';
            } else {
                $error = 'Registration failed';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YIC Library | Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-user-plus"></i>
                <h2>Create Account</h2>
                <p>Join YIC Library today</p>
            </div>
            <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <script>
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 2000);
            </script>
            <?php endif; ?>
            <form method="POST" class="auth-form" id="registerForm">
                <div class="form-row">
                    <div class="form-group"><label>First Name</label><input type="text" name="first_name"
                            placeholder="Ahmed"><small class="error-message" id="firstNameError"></small></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="last_name"
                            placeholder="Ali"><small class="error-message" id="lastNameError"></small></div>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="email"
                        placeholder="student@yic.edu.sa"><small class="error-message" id="regEmailError"></small></div>
                <div class="form-group"><label>Student ID</label><input type="text" name="student_id"
                        placeholder="YIC-2025-XXXX"><small class="error-message" id="studentIdError"></small></div>
                <div class="form-group"><label>Password</label><input type="password" name="password"
                        placeholder="Min 6 chars"><small class="error-message" id="regPasswordError"></small></div>
                <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password"
                        placeholder="Re-enter"><small class="error-message" id="confirmPasswordError"></small></div>
                <div class="form-group"><label class="checkbox-label"><input type="checkbox" id="termsCheckbox"> I agree
                        to the Terms</label><small class="error-message" id="termsError"></small></div>
                <button type="submit" class="btn btn-primary btn-full">Register</button>
            </form>
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>

</html>