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

if (isset($_POST['admin_login'])) {
    $email = trim($_POST['admin_email'] ?? '');
    $password = $_POST['admin_password'] ?? '';
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
        if ($isAjax) ajaxResponse(false, $error);
        $_SESSION['admin_error'] = $error;
        header("Location: index.php?admin=true");
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
            // Check role: Only doctors and staff can log in here
            if ($user['role'] !== 'doctor' && $user['role'] !== 'staff') {
                $error = 'This login is for admins only. Please use the client login.';
                if ($isAjax) ajaxResponse(false, $error);
                $_SESSION['admin_error'] = $error;
                header("Location: index.php?admin=true");
                exit();
            }
            
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
            
            // Determine redirect: Doctors to doctor_page.php, Staff/Admins to staff_page.php
            $redirect = ($user['role'] === 'doctor') ? 'doctor_page.php' : 'staff_page.php';
            if ($isAjax) ajaxResponse(true, '', $redirect);
            header("Location: $redirect");
            exit();
        }
    }
    
    $error = 'Incorrect email or password.';
    if ($isAjax) ajaxResponse(false, $error);
    $_SESSION['admin_error'] = $error;
    header("Location: index.php?admin=true");
    exit();
}
?>