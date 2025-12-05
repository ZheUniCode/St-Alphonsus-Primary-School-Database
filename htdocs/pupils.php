<?php
include 'db_connect.php'; //this file to connect to the database

$sql = "SELECT pupilId, pupilNames, pupilAddress, medicalInformation FROM Pupils"; //query to select all pupils
$result = $conn->query($sql); //both execute and store the result of the query

if ($result->num_rows > 0) { //check if there are any results
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Address</th><th>Medical Info</th></tr>"; //table headers made
    while($row = $result->fetch_assoc()) { //loop through each row of the result set
        echo "<tr>
                <td>{$row['pupilId']}</td>
                <td>{$row['pupilNames']}</td>
                <td>{$row['pupilAddress']}</td>
                <td>{$row['medicalInformation']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No pupils found."; //message if no pupils are found
}

$conn->close();   //close the database connection
?>