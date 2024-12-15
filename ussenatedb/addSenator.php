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
    $state = $_POST['statebar'];
    $party = $_POST['partybar'];
    $phone = $_POST['phonebar'];
    $terms = $_POST['termsbar'];
    $class = $_POST['classbar'];
    if(strlen($name) > 256 || strlen($state) > 256 || strlen($party) > 1 
    || strlen($phone) > 12 || $terms > 20 || strlen($class) > 3) {
        include('admin.html');
        echo("<h4 class=subTitleFailed> INSERT FAILED, Input Size Invalid </h4>");
        exit;
    }

    //make sure foreign key state is valid
    $stmt = $pdo->prepare("SELECT * FROM State WHERE Name = :state");
    $stmt->bindParam(':state', $state);
    $stmt->execute();
    if($stmt->rowCount() == 0){
        include('admin.html');
        echo("<h4 class=subTitleFailed> INSERT FAILED, State Invalid </h4>");
        exit;
    }

    // Check if Senator already exists
    $stmt = $pdo->prepare("SELECT * FROM Senator WHERE Name= :name");
    $stmt->bindParam(':name', $name);

    // Execute the query
    $stmt->execute();
    $action = "";
    if ($stmt->rowCount() > 0) { //senator already exists, edit info
        $stmt = $pdo->prepare("UPDATE Senator 
        SET Phone_Number = :phone, Class = :class, Terms_Served = :terms, Party = :party, State = :state
        WHERE Name = :name");
        $action = " Edited";
    } else { //create senator
        $stmt = $pdo->prepare("INSERT INTO Senator (Name, Phone_Number, Class, Terms_Served, Party, State)
        VALUES (:name, :phone, :class, :terms, :party, :state)");
        $action = " Added";
    }
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':class', $class);
    $stmt->bindParam(':terms', $terms);
    $stmt->bindParam(':party', $party);
    $stmt->bindParam(':state', $state);

    //update/create senator
    $stmt->execute();

    //Prints Results
    include 'admin.html';
    echo("<h4 class=bodyText> Senator ".$name.$action."</h4>");
    $stmt = $pdo->prepare("SELECT * FROM Senator WHERE State = :state");
    $stmt->bindParam(":state", $state);
    $stmt->execute();
    if($stmt->rowCount() < 1 || $stmt->rowCount() > 2){
        echo("<h4 class=subTitleFailed> Warning! State '".$state."' Senator Amount Outside Accepted Range: ".$stmt->rowCount()."</h4>");
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;
?>
