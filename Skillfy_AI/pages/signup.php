<?php
session_start();

include_once '../includes/db_conn.php';
include_once '../includes/functions.php';

// Process the form if it’s submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    // Validate required fields
    if (!$firstName || !$lastName || !$email || !$password) {
        $_SESSION['error'] = "All fields are required.";
        // In production, you might want to redirect back to signup
    } else {
        // Optionally, add more validation: email format, password strength, etc.

        // Check if the email is already in use
        $checkSql = "SELECT email FROM users WHERE email = :email";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            // Email already exists
            $_SESSION['error'] = "Email already in use. Please choose another.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into the database
            $sql = "INSERT INTO users (first_name, last_name, email, password) 
                    VALUES (:first_name, :last_name, :email, :password)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name',  $lastName);
            $stmt->bindParam(':email',      $email);
            $stmt->bindParam(':password',   $hashedPassword);

            if ($stmt->execute()) {
                // Registration successful
                $_SESSION['success'] = "Account created successfully! You can now log in.";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again.";
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
    <title>Sign Up - Skillfy</title>
    <link rel="stylesheet" href="../styles/signup.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <a href="home.html" class="logo">
                <svg class="logo-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" 
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                <span>Skillfy</span>
            </a>
        </div>
    </header>

    <main class="main-content">
        <div class="signup-card">
            <div class="card-header">
                <h1 class="card-title">Create an account</h1>
                <p class="card-subtitle">Start your career growth journey with Skillfy</p>

                <!-- Display error or success messages -->
                <?php if (!empty($_SESSION['error'])): ?>
                    <p style="color: red;">
                        <?= $_SESSION['error']; ?>
                    </p>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['success'])): ?>
                    <p style="color: green;">
                        <?= $_SESSION['success']; ?>
                    </p>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
            </div>

            <form action="" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="firstName">First name</label>
                        <input type="text" id="firstName" name="firstName" class="form-input" placeholder="First Name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="lastName">Last name</label>
                        <input type="text" id="lastName" name="lastName" class="form-input" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="name@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                    <p class="password-requirements">
                        Must be at least 8 characters with 1 uppercase, 1 number, and 1 special character
                    </p>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" required>
                    <div>
                        <label for="terms" class="terms-text">
                            I agree to the 
                            <a href="#" class="terms-link">Terms of Service</a> 
                            and 
                            <a href="#" class="terms-link">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Create account</button>
            </form>

            <div class="divider">
                <span>OR SIGN UP WITH</span>
            </div>

            <div class="social-signup">
                <button class="social-btn">
                    <!-- Google Icon -->
                    <svg class="social-icon" xmlns="http://www.w3.org/2000/svg" 
                         viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Google
                </button>
                <button class="social-btn">
                    <!-- Facebook Icon -->
                    <svg class="social-icon" xmlns="http://www.w3.org/2000/svg" 
                         viewBox="0 0 24 24">
                        <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z" fill="#1877F2"/>
                    </svg>
                    Facebook
                </button>
            </div>

            <div class="card-footer">
                <p>Already have an account? <a href="login.php" class="login-link">Sign in</a></p>
            </div>
        </div>
    </main>
</body>
</html>
