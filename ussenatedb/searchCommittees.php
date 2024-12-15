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
    $cName = $_POST['committeeNameSearch'];
    $cName = '%' . $cName . '%';
    $sName = $_POST['senatorSearch'];
    $sName = '%' . $sName . '%';
    $comFilter = $_POST['committeeFilter'];

    // Prepare SQL query
    $comOnly = true;
    if($comFilter == "com"){
        $stmt = $pdo->prepare("SELECT Committee_Name FROM Committee WHERE UPPER(Committee_Name) LIKE UPPER(:cName)
        AND Committee_ID IN (SELECT Committee_ID FROM Is_Member Where UPPER(Senator_Name) LIKE UPPER(:sName))");
    } else {
        $stmt = $pdo->prepare("SELECT Subcommittee_Name FROM Subcommittee WHERE UPPER(Subcommittee_Name) LIKE UPPER(:cName)
        AND Subcommittee_ID IN (SELECT Subcommittee_ID FROM Is_Member Where UPPER(Senator_Name) LIKE UPPER(:sName))");
        $comOnly = false;
    } 
    $stmt->bindParam(':cName', $cName);
    $stmt->bindParam(':sName', $sName);
    $cNames = array();
    // Execute the query
    $stmt->execute();
    // Save Committee names to array:
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ 
        if($comOnly){
            array_push($cNames, $row["Committee_Name"]);
        } else {
            array_push($cNames, $row["Subcommittee_Name"]);
        }
    }
    //Prints Query as HTML Table
    include 'committee.html';
    echo("<h3 class='title'>Results:</h3>");
    //iterate through committees printing members:
    foreach($cNames as $x){
        $cid = 0;
        if (!$comOnly) {
            $stmt = $pdo->prepare("SELECT Subcommittee_ID FROM Subcommittee WHERE Subcommittee_Name = :x");
            $stmt->bindParam(':x', $x);
        } else { 
            $stmt = $pdo->prepare("SELECT Committee_ID FROM Committee WHERE Committee_Name = :x");
            $stmt->bindParam(':x', $x);
        }
        echo("<h5 class=subTitle>".$x."</h5>");
        $stmt->execute();
        $prefix = "C";
        if(!$comOnly){
            $prefix = "Subc";
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cid = $row[$prefix.'ommittee_ID'];
        if($comOnly){
            $stmt = $pdo->prepare("SELECT Senator_Name FROM Is_Member WHERE Committee_ID = :cid");
        }
        else{
            $stmt = $pdo->prepare("SELECT Senator_Name FROM Is_Member WHERE Subcommittee_ID = :cid");
        }
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        echo("<h5 class=bodyText> Chair: ");
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            echo($row["Senator_Name"]."<br>");
        }
        }
        echo("</h5>");
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;
?>
