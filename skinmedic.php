<?php
session_start();
require_once 'config.php';

// Helper function to return JSON response for AJAX
function ajaxResponse($success, $error = '', $redirect = '') {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'error' => $error, 'redirect' => $redirect]);
    exit();
}

// Check if request is AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (isset($_POST['signup'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');
    
    // Basic validation
    if (empty($email) || empty($password) || empty($confirmPassword) || empty($fullname)) {
        $error = 'All fields are required.';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['signup_error'] = $error;
        header("Location: index.php");
        exit();
    }
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['signup_error'] = $error;
        header("Location: index.php");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['signup_error'] = $error;
        header("Location: index.php");
        exit();
    }
    
    // Split fullname into firstName and lastName
    $nameParts = explode(' ', $fullname, 2);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Check for existing email
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error = 'Email is already registered!';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['signup_error'] = $error;
        header("Location: index.php");
        exit();
    }
    
    // Insert new user with defaults for missing fields
    $role = 'patient'; // Default role
    $gender = 'Not specified'; // Default
    $address = 'Not provided'; // Default
    $phone_no = 'Not provided'; // Default
    $stmt = $conn->prepare("INSERT INTO users (email, firstName, lastName, password, gender, address, phone_no, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $email, $firstName, $lastName, $hashedPassword, $gender, $address, $phone_no, $role);
    if ($stmt->execute()) {
        // Auto-login after signup: Set session and redirect
        $userId = $stmt->insert_id;
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['gender'] = $gender;
        $_SESSION['address'] = $address;
        $_SESSION['phone_no'] = $phone_no;
        $_SESSION['profile_image'] = null; // Default
        $_SESSION['role'] = $role;
        
        // Determine redirect based on role
        $redirect = ($role === 'doctor') ? 'doctor_page.php' : (($role === 'staff') ? 'staff_page.php' : 'patient_page.php');
        if ($isAjax) ajaxResponse(true, '', $redirect);
        header("Location: $redirect");
        exit();
    } else {
        $error = 'Signup failed. Please try again.';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['signup_error'] = $error;
        header("Location: index.php");
        exit();
    }
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['login_error'] = $error;
        header("Location: index.php");
        exit();
    }
    
    // Fetch user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['firstName'] = $user['firstName'];
            $_SESSION['lastName'] = $user['lastName'];
            $_SESSION['gender'] = $user['gender'];
            $_SESSION['address'] = $user['address'];
            $_SESSION['phone_no'] = $user['phone_no'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['role'] = $user['role'];
            
            // Determine redirect based on role
            $redirect = ($user['role'] === 'doctor') ? 'doctor_page.php' : (($user['role'] === 'staff') ? 'staff_page.php' : 'patient_page.php');
            if ($isAjax) ajaxResponse(true, '', $redirect);
            header("Location: $redirect");
            exit();
        }
    }
    
    $error = 'Incorrect email or password.';
    if ($isAjax) ajaxResponse(false, $error);
    $_SESSION['login_error'] = $error;
    header("Location: index.php");
    exit();
}
?>