
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pupils</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php
include 'db_connect.php'; //this file to connect to the database

   $sql = "SELECT classId, teacherId, className, classCapacity FROM Class";
   $result = $conn->query($sql);

   // Check if there are any rows in the result
   if ($result->num_rows > 0) {
         echo "<table border='1'><tr><th>Class ID</th><th>Teacher ID</th><th>Class Name</th><th>Class Capacity</th></tr>";
         while ($row = $result->fetch_assoc()) {
               echo "<tr>
                       <td>{$row['classId']}</td>
                       <td>{$row['teacherId']}</td>
                       <td>{$row['className']}</td>
                       <td>{$row['classCapacity']}</td>
                     </tr>";
         }
         echo "</table>";   //table footer made 
   } else {
    echo "No classes found."; //message if no classes are found
}

$conn->close();   //close the database connection
?>
</body>
</html>