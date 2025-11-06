<?php

session_start();
require_once 'config.php';

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone_no = $_POST['phone_no'];
    $role = $_POST['role'];

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['signup_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'signup';
    } else {
        $conn->query("INSERT INTO users (email, firstName, lastName, password, gender, address, phone_no, role) VALUES ('$email', '$firstName', '$lastName', '$password', '$gender', '$address', '$phone_no', '$role')");
    }

    header("Location: login_signup.php");
    exit();
}

if (isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['firstName'] = $user['firstName'];
            $_SESSION['lastName'] = $user['lastName'];
            $_SESSION['gender'] = $user['gender'];
            $_SESSION['address'] = $user['address'];
            $_SESSION['phone_no'] = $user['phone_no'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['role'] = $user['role'];


            if ($user['role'] === 'doctor') {
                header("Location: doctor_page.php");
            } elseif ($user['role'] === 'staff') {
                header("Location: staff_page.php");
            } else {
                header("Location: patient_page.php");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

?>