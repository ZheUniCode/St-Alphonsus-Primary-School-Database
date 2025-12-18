<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: logIn.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St Alphonsus School Management System</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <main>
        <div class="card" style="max-width: 600px; margin: 40px auto;">
            <h1 style="text-align:center;">St Alphonsus School Management System</h1>
            
            <?php
            //display the teacher's name
            include "db_connect.php";
            $teacherId = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT teacherNames FROM teachers WHERE teacherId = ?");
            $stmt->bind_param("i", $teacherId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo "<p style='font-size:24px;'><strong>Hello, " . ($row['teacherNames']) . "!</strong></p>";
            }
            $stmt->close();
            echo "<p style='text-align:center;'>Welcome to the staff dashboard. Please select an action below:</p>";
            echo "<h2 style='margin-top:32px;'>Your Classes</h2>";
            $stmt = $conn->prepare("SELECT c.classId, c.className, c.classCapacity, t.teacherId, t.teacherNames FROM Class c JOIN teachers t ON c.teacherId = t.teacherId WHERE c.teacherId = ?");
            $stmt->bind_param("i", $teacherId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                echo "<table class='card' style='margin-bottom:24px;'>\n"
                    . "<tr>"
                    . "<th>Class ID</th>"
                    . "<th>Teacher</th>"
                    . "<th>Class Name</th>"
                    . "<th>Class Capacity</th>"
                    . "</tr>";
                //loops through each class and displays in a table row
                while ($row = $result->fetch_assoc()) {
                    $teacherDisplay = ($row['teacherId']) . ' - ' . ($row['teacherNames']);
                    echo "<tr>";
                    echo "<td>" . ($row['classId']) . "</td>";
                    echo "<td>" . $teacherDisplay . "</td>";
                    echo "<td>" . ($row['className']) . "</td>";
                    echo "<td>" . ($row['classCapacity']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>You are not assigned to any classes.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
            <div style="margin-top:32px;">
                <a href="classes.php" class="button" style="display:block; margin-bottom:12px; text-align:center;">Manage Classes</a>
                <a href="pupils.php" class="button" style="display:block; margin-bottom:12px; text-align:center;">Manage Students</a>
                <a href="attendance.php" class="button" style="display:block; margin-bottom:12px; text-align:center;">Record Attendance</a>
                <a href="parents.php" class="button" style="display:block; margin-bottom:12px; text-align:center;">Manage Parents</a>
                <a href="logIn.php?logout=1" class="button" style="display:block; background:#ffa200; color:#222; text-align:center;">Logout</a>
            </div>
        </div>
    </main>
</body>
</html>















