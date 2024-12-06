<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if ($username && $email && $password) {
    $db = new mysqli("localhost", "pixeladmin", "changeme", "pixelrun");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    echo "gets here (connected) <br/>" ;

    $stmt = $db->prepare("SELECT username FROM user_auth WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['errorMsg'] = "Username already exists!";
        header("Location:../index.php");
        exit();
    }
echo "gets here (no existing user)<br/>" ;
    $stmt->close();

    $pepper = "cosc213";
    $hashedPwd = password_hash($password . $pepper, PASSWORD_BCRYPT);
    echo "gets here (pwd hashed)<br/>" ;
    $stmt = $db->prepare("INSERT INTO user_auth (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPwd, $email);
    echo "gets here (bind params)<br/>" ;
    if ($stmt->execute()) {
        echo "gets here (inserted values)<br/>" ;
        $_SESSION["registerSuccess"] = "Registration was successful!";
    } else {
        echo "gets here (insert error)$stmt->error;<br/>" ;
        $_SESSION['errorMsg'] = "Error during registration: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    $_SESSION['errorMsg'] = "No values provided!";
}
echo "gets here (finish)<br/>" ;
header("Location:../index.php");
exit();
