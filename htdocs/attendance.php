<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: logIn.php');
	exit();
}
include 'db_connect.php';

$teacherId = $_SESSION['user_id'];

//gets what classes the teacher that is logged in is assigned to
$stmt = $conn->prepare("SELECT classId, className FROM Class WHERE teacherId = ?");
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$result = $stmt->get_result();
$classes = [];
while ($row = $result->fetch_assoc()) {
	$classes[] = $row;
}
$stmt->close();


//this takes the data submitted from the attendance form and saves it into the database 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendanceDate'])) {
	$attendanceDate = $_POST['attendanceDate'];
	foreach ($_POST['attendance'] as $classId => $pupils) {
		foreach ($pupils as $pupilId => $statuses) {
			// Only one status should be saved per pupil per day. If multiple are checked, pick the first.
			$status = null;
			foreach (["Present", "Absent", "Late", "Excused"] as $s) {
				if (isset($statuses[$s])) {
					$status = $s;
					break;
				}
			}
			if ($status) {
				$stmt = $conn->prepare("REPLACE INTO Attendance (classId, pupilId, teacherId, attendanceDate, status) VALUES (?, ?, ?, ?, ?)");
				$stmt->bind_param("iiiss", $classId, $pupilId, $teacherId, $attendanceDate, $status);
				$stmt->execute();
				$stmt->close();
			}
		}
	}
	echo "<div class='card'><p class='success'>Attendance recorded for $attendanceDate.</p></div>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Record Attendance</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<div class="card" style="max-width:900px;margin:40px auto;">
		<h1>Record Attendance</h1>
		<form method="POST" action="">
			<label for="attendanceDate"><b>Date:</b></label>
            <!-- this php function that formats a local date -->
			<input type="date" id="attendanceDate" name="attendanceDate" class="input-boxes" value="<?php echo date('Y-m-d'); ?>" required> 
			<br><br>
			<?php
			if (empty($classes)) {//if the teacher is not assigned to any classes
				echo "<p>You are not assigned to any classes.</p>";
			} else { //if the teacher is assigned to classes
				foreach ($classes as $class) { //loop through each class
					$classId = $class['classId']; //get the class ID
					$className = ($class['className']); //get the class name
					echo "<h2 style='margin-top:32px;'>Attendance for Class: $className</h2>"; //display the class name

					$pupilStmt = $conn->prepare("SELECT pupilId, pupilNames FROM Pupils WHERE classId = ?"); //get pupils in the class
					$pupilStmt->bind_param("i", $classId); 
					$pupilStmt->execute(); //execute the query
                    //display the pupils in a table with checkbox buttons for attendance status
					$pupilResult = $pupilStmt->get_result();
					if ($pupilResult->num_rows > 0) {
						echo "<table class='card'>
                                <tr>
                                    <th>Pupil Name</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Excused</th>
                                </tr>";
                        //loop through each pupil and create a row with checkbox buttons for attendance status
						while ($pupil = $pupilResult->fetch_assoc()) {
							$pupilId = $pupil['pupilId'];
							$pupilName = ($pupil['pupilNames']);
							echo "<tr>";
							echo "<td>$pupilName</td>";
							foreach (["Present", "Absent", "Late", "Excused"] as $status) {
								echo "<td>
                                        <input type='checkbox' name='attendance[$classId][$pupilId][$status]' value='1' class='checkbox'>
                                    </td>";
							}
							echo "</tr>";
						}
						echo "</table>";
					} else {
						echo "<p>No pupils found in this class.</p>";
					}
					$pupilStmt->close();
				}
				echo "<br><input type='submit' value='Save Attendance' class='button'>";
			}
			?>
		</form>
	</div>
    
    

</body>

<footer>
        <div style="max-width:900px;margin:40px auto;margin-top:24px;">
            <a href="main.php" class="button">Back to Dashboard</a>
        </div>
    </footer>
</html>
