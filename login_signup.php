<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
];
$active_Form = $_SESSION['active_Form'] ?? 'login';


function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $active_Form) {
    return $formName === $active_Form ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>SkinMedic</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

        <div class="container">
        <div class="form-box <?= isActiveForm('login', $active_Form) ?>" id="login-form">
            <form action="skinmedic.php" method="post">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="#" onclick="showForm('signup-form')">Signup</a></p>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="form-box <?= isActiveForm('signup', $active_Form) ?>" id="signup-form">
            <form action="skinmedic.php" method="post">
                <h2>Signup</h2>
                <?= showError($errors['signup']); ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="firstname" placeholder="Firstname" required>
                <input type="text" name="lastname" placeholder="Lastname" required>
                <select name="gender" required>
                    <option value="">Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="others">Others</option>
                </select>
                <input type="text" name="address" placeholder="Address" required>
                <input type="text" name="phone_no" placeholder="Phone_No" required>
                <select name="role" required>
                    <option value="">Role</option>
                    <option value="patient">Patient</option>
                    <option value="staff">Staff</option>
                    <option value="doctor">Doctor</option>
                </select>
                <button type="submit" name="signup">Signup</button>
                <p>Already have an account? <a href="#" onclick="showForm('login-form')"> Login</a></p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>

    </body>
    </html>