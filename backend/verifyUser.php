<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if ($username && $password) {
    $db = new mysqli("localhost", "pixeladmin", "changeme", "pixelrun");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $stmt = $db->prepare("SELECT username, password FROM user_auth WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($authUsername, $authPwd);
    $stmt->fetch();


    if (!$authUsername) {
        $_SESSION['errorMsg'] = "Username does not exist!";
        $stmt->close();
        $db->close();
        header("Location: ../index.php");
        exit();
    }


    $pepper = "cosc213";
    if (password_verify($password . $pepper, $authPwd)) {
        $_SESSION['username'] = $authUsername;
        $stmt->close();
        $db->close();
        header("Location: ../pages/game.php");
        exit();
    } else {
        $_SESSION['errorMsg'] = "Incorrect credentials!";
        $stmt->close();
        $db->close();
        header("Location: ../index.php");
        exit();
    }

} else {
    $_SESSION['errorMsg'] = "Please enter both username and password!";
    header("Location: ../index.php");
    exit();
}
