<?php
//protects the page so only logged in users can view it, if not they are sent to the login page
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: logIn.php');
    exit();
}
include 'db_connect.php';

//fetch all teachers once for dropdowns
$teacherList = [];
$teacherListResult = $conn->query("SELECT teacherId, teacherNames FROM teachers");
if ($teacherListResult && $teacherListResult->num_rows > 0) { //checks if there are results
    while($teacher = $teacherListResult->fetch_assoc()) { //loops through each teacher and adds to the list
        $teacherList[] = $teacher; //adds teacher to the list 
    }
}


////////////////////// ////////////
/// Edit Class Logic /////////////
////////////////////// ////////////
if (//checks if teh request method is post and the editClass fields are set
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['editClass']) && isset($_POST['editClassId']) && isset($_POST['editClassName']) && isset($_POST['editClassCapacity']) && isset($_POST['editTeacherId'])
) {
    //gets the values from the form and validates them
    $editClassId = intval($_POST['editClassId']); //convert to integer
    $editClassName = trim($_POST['editClassName']); //trim whitespace
    $editClassCapacity = intval($_POST['editClassCapacity']); 
    $editTeacherId = intval($_POST['editTeacherId']);
    $editClassErrors = []; //array to hold any validation errors

    //validate teh users inputs
    if (empty($editClassName)) {
        $editClassErrors[] = "Class name is required.";
    }
    if ($editClassCapacity <= 0) {
        $editClassErrors[] = "Class capacity must be a positive number.";
    }
    if ($editTeacherId <= 0) {
        $editClassErrors[] = "Teacher ID is required.";
    }
    //when no errors proceeds to update the class in the database
    if (empty($editClassErrors)) {
        $stmt = $conn->prepare("UPDATE Class SET className=?, classCapacity=?, teacherId=? WHERE classId=?"); //prepare the update statement
        $stmt->bind_param("siii", $editClassName, $editClassCapacity, $editTeacherId, $editClassId);//bind the parameters
        //relods the page with a success message if the update is successful or shows an error
        if ($stmt->execute()) {
            $_SESSION['success'] = "Class updated successfully.";
            header("Location: classes.php");
            exit();
        } else {
            $editClassErrors[] = "Error updating class: " . ($stmt->error);
        }
        $stmt->close();
    }
    //shows any errors that happend during the edit process
    if (!empty($editClassErrors)) {
        echo "<div class='card' style='background:#ff000022;'><b>Edit Class Error(s):</b><ul>";
        foreach ($editClassErrors as $error) {
            echo "<li class='error'>" . ($error) . "</li>";
        }
        echo "</ul></div>";
    }
}


////////////////////// ////////////
/// Add Class Logic /////////////   
////////////////////// ////////////
$addClassErrors = []; //empty array to store any errors that occur when adding a new class
$removeClassErrors = []; //any errors that occur when removing a class
$success = ''; //success message

//checks if teh request method is post and the className field is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['className'])) 
    {
        //gets the values from the form and validates them
    $className = isset($_POST['className']) ? trim($_POST['className']) : '';
    $classCapacity = isset($_POST['classCapacity']) ? intval($_POST['classCapacity']) : 0;
    $teacherId = isset($_POST['teacherId']) ? intval($_POST['teacherId']) : 0;
    
    if (empty($className)) { //checks that the class name is not empty
        $addClassErrors[] = "Class name is required.";
    }
    if ($classCapacity <= 0) { //checks that the capacity is a positive number
        $addClassErrors[] = "Class capacity must be a positive number.";
    }
    if ($teacherId <= 0) { //checks that a teacher is selected in the dropdown and uses their id
        $addClassErrors[] = "Teacher ID is required.";
    }

    //if no errors then add the class to the database
    if (empty($addClassErrors)) {
        $stmt = $conn->prepare("INSERT INTO Class (teacherId, className, classCapacity) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $teacherId, $className, $classCapacity);
        if ($stmt->execute()) {
            $success = "Class added successfully.";
        } else {
            $addClassErrors[] = "Error adding class: " . ($stmt->error);
        }
        $stmt->close();
    }
}


////////////////////// ////////////
/// Remove Class Logic ////////////
////////////////////// ///////////
//checks if the request method is post and the removeClass field is set
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['classId']) && isset($_POST['removeClass'])
) {
    //validate and get the class ID to remove 
    $classId = intval($_POST['classId']);
    //check if any pupils are assigned to this class
    $pupilCheck = $conn->prepare("SELECT COUNT(*) FROM Pupils WHERE classId = ?");
    $pupilCheck->bind_param("i", $classId);
    $pupilCheck->execute();
    $pupilCheck->bind_result($pupilCount);
    $pupilCheck->fetch();
    $pupilCheck->close();
    
    //if there are pupils assigned does not allow deletion
    if ($pupilCount > 0) {
        $removeClassErrors[] = "Cannot delete class: there are pupils assigned to this class. Please reassign or remove all pupils from this class first.";
    } else {
        $stmt = $conn->prepare("DELETE FROM Class WHERE classId = ?");
        $stmt->bind_param("i", $classId);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Class removed successfully.";
            // Redirect to avoid resubmission on refresh
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            $removeClassErrors[] = "Error removing class: " . ($stmt->error);
        }
        $stmt->close();
    }
}

/////////////////////// ////////////
/// Display Classes Logic /////////
/////////////////////// ////////////

// Retrieve all classes with teacher names next to thier IDs
$sql = "SELECT c.classId, c.teacherId, t.teacherNames, c.className, c.classCapacity FROM Class c LEFT JOIN teachers t ON c.teacherId = t.teacherId";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1 class="card">Manage Classes</h1>

<!-- html for adding and removing classes dropdowns -->
<div class='row'>
    <!-- add  new class form -->
    <details class='form-details'>
        <summary class='button'>Add New Class</summary>
        <form method="POST" action="" class="card">
            <label>Class Name:<br>
                <input type="text" name="className" class="input-boxes" required>
            </label>
            <label>Class Capacity:<br>
                <input type="number" name="classCapacity" class="input-boxes" min="1" required>
            </label>
            <label>Teacher:<br>
                <select name="teacherId" class="input-boxes" required>
                    <option value="">Select a teacher</option>
                    <!-- php to show all the teacher options -->
                    <?php
                    foreach ($teacherList as $teacher) {
                        echo "<option value='" . ($teacher['teacherId']) . "'";
                        if (isset($_POST['teacherId']) && $_POST['teacherId'] == $teacher['teacherId']) echo " selected";
                        echo ">" . ($teacher['teacherId']) . " - " . ($teacher['teacherNames']) . "</option>";
                    }
                    ?>
                </select>
            </label>
            <input type="submit" value="Add Class" class="button">
        </form>
    </details>

    <!-- Remove Class Form -->
    <details class='form-details'>
        <summary class='button'>Remove Class</summary>
        <form method="POST" action="" class="card">
            <label for="classId">Select Class:<br>
                <select id="classId" name="classId" class="input-boxes" required>
                    <!-- php to show all the class options -->
                    <?php
                    $classListResult = $conn->query("SELECT classId, className FROM Class");
                    if ($classListResult && $classListResult->num_rows > 0) {
                        while($class = $classListResult->fetch_assoc()) {
                            echo "<option value='" . ($class['classId']) . "'>" . ($class['classId']) . " - " . ($class['className']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <input type="hidden" name="removeClass" value="1">
            <input type="submit" value="Remove Class" class="button">
        </form>
    </details>
</div>





<!-- php to display error and success messages when adding or removing classes -->
<?php
if (!empty($addClassErrors)) {
    echo "<div class='card' style='background:#ff000022;'><b>Add Class Error(s):</b><ul>";
    foreach ($addClassErrors as $error) {
        echo "<li class='error'>" . ($error) . "</li>";
    }
    echo "</ul></div>";
}
if (!empty($removeClassErrors)) {
    echo "<div class='card' style='background:#ff000022;'><b>Remove Class Error(s):</b><ul>";
    foreach ($removeClassErrors as $error) {
        echo "<li class='error'>" . ($error) . "</li>";
    }
    echo "</ul></div>";
}
if ($success) {
    echo "<p class='success'>" . ($success) . "</p>";
}
//display success message after editing a class
if (isset($_SESSION['success'])) {
    echo "<p class='success'>" . ($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}
?>



<!-- ------------------------------------------------ -->
<!-- shows the whole table with classes and their details -->
 <!-- ---------------------------------------------- -->
<?php
echo "<h2>Classes Information List</h2>";

//Shows the edit form when the 3 dots button under edit is clicked
$editClassId = isset($_GET['editClassId']) ? intval($_GET['editClassId']) : 0;
$editClassData = null;
if ($editClassId > 0) {
    $editResult = $conn->query("SELECT * FROM Class WHERE classId = $editClassId");
    if ($editResult && $editResult->num_rows > 0) {
        $editClassData = $editResult->fetch_assoc();
    }
}

// Display classes in a table
if ($result && $result->num_rows > 0) {
    echo "<table class='card'>
        <tr>
            <th>Class ID</th>
            <th>Teacher</th
            ><th>Class Name</th>
            <th>Class Capacity</th>
            <th>Edit</th>
        </tr>";
    
    //loops through each class and displays in a table row
    while ($row = $result->fetch_assoc()) {
        $teacherDisplay = ($row['teacherId']) . ' - ' . (isset($row['teacherNames']) ? ($row['teacherNames']) : 'Unknown');
        echo "<tr>\n";
        echo "<td>" . ($row['classId']) . "</td>\n";
        echo "<td>" . ($teacherDisplay) . "</td>\n";
        echo "<td>" . ($row['className']) . "</td>\n";
        echo "<td>" . ($row['classCapacity']) . "</td>\n";

        //Edit button with three dots to open the edit form
        echo "<td>"
            . "<form method='get' action=''>"
            . "<input type='hidden' name='editClassId' value='" . ($row['classId']) . "'>"
            . "<button type='submit' class='dots-container' style='background:none;border:none;'>"
            . "<div class='dot'></div>"
            . "<div class='dot'></div>"
            . "<div class='dot'></div>"
            . "</button>"
            . "</form>";
        //checks if the current class row is the one being edited
        if ($editClassId == $row['classId'] && $editClassData) {
            echo "<div class='card edit-form-popup' style='padding:10px;'>"
                . "<form method='post' action=''>"
                . "<input type='hidden' name='editClassId' value='" . ($editClassData['classId']) . "'>"
                . "<label>Class Name:<br><input type='text' name='editClassName' value='" . ($editClassData['className']) . "' required></label><br>"
                . "<label>Class Capacity:<br><input type='number' name='editClassCapacity' class='input-boxes' value='" . ($editClassData['classCapacity']) . "' min='1' required></label><br>"
                . "<label>Teacher:<br><select name='editTeacherId' class='input-boxes' required>";
            //php to show all the teacher options in the edit form dropdown
            foreach ($teacherList as $teacher) {
                $selected = ($editClassData['teacherId'] == $teacher['teacherId']) ? 'selected' : '';
                echo "<option value='" . ($teacher['teacherId']) . "' $selected>" . ($teacher['teacherId']) . " - " . ($teacher['teacherNames']) . "</option>";
            }
            //closes the select and form
            echo "</select></label><br>"
                . "<input type='submit' name='editClass' value='Save' class='button'>"
                . "<a href='?" . http_build_query(array_diff_key($_GET, ['editClassId'=>1])) . "' class='button' style='margin-left:8px;text-decoration:none;color:black;'>Cancel</a>"
                . "</form>"
                . "</div>";
        }
        echo "</td>\n";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='card'><p>No classes found.</p></div>";
}
$conn->close();
?>
</body>

<footer>
        <div style="max-width:900px;margin:40px auto;margin-top:24px;">
            <a href="main.php" class="button">Back to Dashboard</a>
        </div>
    </footer>
</html>