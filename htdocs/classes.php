<?php
   include 'db_connect.php';

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