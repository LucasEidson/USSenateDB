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
    $name = $_POST['namebar'];

    // Ensure no constraints will be violated
    $stmt = $pdo->prepare("SELECT * FROM Committee AS c, Subcommittee AS s, Leadership AS l WHERE c.Chair_Name = :name OR s.Chair_Name = :name OR l.Senator_Name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    if($stmt->rowCount() > 0){
        echo("<h4 class=subTitleFailed> Delete Failed, Senator is Chair of (Sub)Committee or Senate Leader</h4>");
        exit;
    }
    //deleting senator
    $stmt = $pdo->prepare("DELETE FROM Is_Member WHERE Senator_Name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $stmt = $pdo->prepare("DELETE FROM Senator WHERE Name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    
    //Print Results
    include 'admin.html';
    echo("<h4 class=bodyText> Senator ".$name." Removed</h4>");

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;
?>
