<?php
include 'includes/session.php';

if (isset($_POST['signup'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    // Check if passwords match
    if ($password != $repassword) {
        $_SESSION['error'] = 'Passwords did not match';
        header('location: signup.php');
        exit();
    }

    // Open database connection
    $conn = $pdo->open();

    // Check if email is already taken
    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();
    if ($row['numrows'] > 0) {
        $_SESSION['error'] = 'Email already taken';
        header('location: signup.php');
        exit();
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user information into the database
    try {
        $stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, created_on) VALUES (:email, :password, :firstname, :lastname, NOW())");
        $stmt->execute(['email' => $email, 'password' => $password, 'firstname' => $firstname, 'lastname' => $lastname]);

        // Optional: You may perform additional actions after successful registration

        unset($_SESSION['firstname']);
        unset($_SESSION['lastname']);
        unset($_SESSION['email']);

        $_SESSION['success'] = 'Account created successfully.';
        header('location: signup.php');
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header('location: register.php');
    }

    // Close database connection
    $pdo->close();
} else {
    $_SESSION['error'] = 'Fill up signup form first';
    header('location: signup.php');
}
?>
