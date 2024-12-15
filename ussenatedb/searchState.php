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
    $stateName = $_POST['stateSearch'];
    $stateName = '%' . $stateName . '%';

    // Prepare SQL query
    $stmt = $pdo->prepare("SELECT Name FROM State WHERE Name LIKE :name");
    $stmt->bindParam(':name', $stateName);

    //Execute query and add to array
    $stmt->execute();
    $stateNames = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ 
        array_push($stateNames, $row["Name"]);
    }

    //Prints Query as HTML Table
    include("state.html");
    echo("<h3 class='title'>Results:</h3>");
    foreach($stateNames as $x){
        echo("<h4 class=subTitle>".$x."</h4>"); //print state name
        $stmt = $pdo->prepare("SELECT Senate_Address, Senate_Email, Senate_Website FROM State WHERE Name LIKE :x");
        $stmt->bindParam(':x', $x);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            //print state info:
            echo("<h5 class=bodyText> Capitol Address: ".$row['Senate_Address']."<br>");
            if($row['Senate_Email'] != null){
                echo("Senate Email: ".$row['Senate_Email']."<br>");
            }
            echo("Senate Website: ".$row['Senate_Website']."<br>Senators: <br>");
        }
        $stmt = $pdo->prepare("SELECT Name, Party FROM Senator WHERE State LIKE :x");
        $stmt->bindParam(':x', $x);
        $stmt->execute();
        //print senator names:
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo($row["Name"]." (".$row["Party"].") <br>");
        }
    }

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;
?>
