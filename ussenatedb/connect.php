<?php
session_start(); // Start the session at the beginning

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$host = 'localhost:3306';
$dbname = 'leidson_1';
$user = 'root';
$pass = '';

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve form data
    $username = $_POST['usernameBar'];
    $password = $_POST['passwordBar'];

    // Prepare SQL query
    $stmt = $pdo->prepare("SELECT User_Name, Password FROM User WHERE User_Name = :name AND Password = :password");
    $stmt->bindParam(':name', $username);
    $stmt->bindParam(':password', $password);

    //Execute query
    $stmt->execute();
    // Check if user exists
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $row['User_Name'];
        include('admin.html');
    } else {
        include('form.html');
        echo("<h5 class='subTitleFailed'> Log In Failed, Username or Password Incorrect</h5>");
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;
?>
