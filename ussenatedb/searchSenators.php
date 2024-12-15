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
    $name = $_POST['nameSearch'];
    $name = '%' . $name . '%';
    $state = $_POST['stateSearch'];
    $state = '%' . $state . '%';
    $party = $_POST['partySearch'];
    $sort = $_POST['sortSelect'];
    if(array_key_exists('showLeaders', $_POST)) {
        $showLeaders = $_POST['showLeaders'];
    } else {$showLeaders = "";}
    // Prepare SQL query
    if($party == "all"){
        if($showLeaders){
            $stmt = $pdo->prepare("SELECT s.Name, s.Phone_Number, s.Class, s.Terms_Served, s.Party, s.State, l.Type FROM Senator AS s, Leadership AS l WHERE 
            Upper(s.Name) LIKE Upper(:name) AND Upper(s.State) LIKE Upper(:state) AND s.Name IN (
            SELECT Senator_Name FROM Leadership) AND s.Name = l.Senator_Name ORDER BY ".$sort);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM Senator WHERE 
            Upper(Name) LIKE Upper(:name) AND Upper(State) LIKE Upper(:state) ORDER BY ".$sort);
        }
    } else {
        if($showLeaders){
            $stmt = $pdo->prepare("SELECT s.Name, s.Phone_Number, s.Class, s.Terms_Served, s.Party, s.State, l.Type FROM Senator AS s, Leadership AS l WHERE 
            Upper(s.Name) LIKE Upper(:name) AND Upper(s.State) LIKE Upper(:state) AND s.Name IN (
            SELECT Senator_Name FROM Leadership) AND s.Name = l.Senator_Name AND s.Party = :party ORDER BY ".$sort);
            $stmt->bindParam(':party', $party);
        } else {
        $stmt = $pdo->prepare("SELECT * FROM Senator WHERE 
        Upper(Name) LIKE Upper(:name) AND Upper(State) LIKE Upper(:state) AND Party = :party ORDER BY ".$sort);
        $stmt->bindParam(':party', $party);
        }
    }
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':state', $state);
    //$stmt->bindParam(':leadership', $leadership);

    // Execute the query
    $stmt->execute();
    
    //Prints Query as HTML Table
    include 'index.html';
    echo("
        <h3 class='title'>Results:</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Class</th>
                <th>Terms Served</th>
                <th>Party</th>
                <th>State</th>");
            if($showLeaders){echo("<th>Role</th>");}          
            echo("</tr> 
        ");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo("<tr>");
        echo("<td>".$row["Name"]."</td>"."<td>".$row["Phone_Number"]."</td>"."<td>".$row["Class"]."</td>".
        "<td>".$row["Terms_Served"]."</td>"."<td>".$row["Party"]."</td>"."<td>".$row["State"]."</td>");
        if($showLeaders){echo("<td>".$row["Type"]."</td>");}
        echo("</tr>");
    }
    echo("</table>");
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
exit;
?>
