<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pupils</title>
    <link rel="stylesheet" href="styles.css">
</head>
    <body>
        <h1 class="card">Manage Pupils</h1>
        <?php
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: logIn.php');
            exit();
        }
        include 'db_connect.php'; //this file to connect to the database

        

        
        ///////////////////////////
        /// Add Pupil Code ////////
        ///////////////////////////
        //form to add new pupil to the database
        if (
            $_SERVER["REQUEST_METHOD"] == "POST" &&
            isset($_POST['pupilNames']) && isset($_POST['pupilAddress']) && isset($_POST['pupilClassId']) && isset($_POST['addPupil'])
        ) {
            //make variables from the form data and validate them
            $pupilNames = $_POST['pupilNames'];
            $pupilAddress = $_POST['pupilAddress'];
            $medicalInformation = isset($_POST['medicalInformation']) ? $_POST['medicalInformation'] : '';
            $pupilClassId = intval($_POST['pupilClassId']);

            //validate Name with no numbers allowed
            if (preg_match('/[0-9]/', $pupilNames)) {
                echo "<div class='card' style='background:#ff000022;'><p class='error'>Double check your Full Name, must not contain numbers.</p></div>";
            } elseif ($pupilClassId <= 0) {
                echo "<div class='card' style='background:#ff000022;'><p class='error'>Please select a class.</p></div>";
            } else {
                // Insert the new pupil with the selected class
                $stmt = $conn->prepare("INSERT INTO Pupils (pupilNames, pupilAddress, medicalInformation, classId) VALUES (?, ?, ?, ?)"); //prepare is a php function to prepare the sql query
                $stmt->bind_param("sssi", $pupilNames, $pupilAddress, $medicalInformation, $pupilClassId); //bind_param is a php function which works with prepare to bind the variables to the sql query
                if ($stmt->execute()) {//execute is a php function to execute the prepared sql query
                    echo "<div class='card'> 
                            <p class='success'>New pupil created successfully.</p>
                        </div>";
                } else {//error handling
                    echo "<div class='card'>
                            <p class='error'>Error: " . $stmt->error . "</p>
                        </div>";
                }
                $stmt->close();//close the statement
            }
        }


        ///////////////////////////
        /// Remove Pupil Code /////
        ///////////////////////////
        if (
            $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pupilId']) && isset($_POST['removePupil']) //check if the form is submitted via POST and the pupilId and removePupil fields are set
        ) {//make variable from the form data
            $pupilId = $_POST['pupilId'];
            //delete attendance records for this pupil to avoid foreign key constraint errors
            $delAttendance = $conn->prepare("DELETE FROM Attendance WHERE pupilId = ?");
            $delAttendance->bind_param("s", $pupilId);
            $delAttendance->execute();
            $delAttendance->close();

            // Then, delete any family members associated with the pupil
            $delFamily = $conn->prepare("DELETE FROM family WHERE pupilId = ?");
            $delFamily->bind_param("s", $pupilId);
            $delFamily->execute();
            $delFamily->close();
            //then delete pupil
            $stmt = $conn->prepare("DELETE FROM Pupils WHERE pupilId = ?");
            $stmt->bind_param("s", $pupilId);
            if ($stmt->execute()) {
            //when the deletion is successful, it redirects the user to tge same page to prevent resubmission on refresh            
                $_SESSION['success'] = "Pupil updated successfully.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
            else {//error handling
                echo "<div class='card'>
                        <p class='error'>Error: " . $stmt->error . "</p>
                    </div>";
            }
            $stmt->close();
        }


        // Fetch all classes from database into $classList BEFORE rendering the Add New Pupil form
        $classList = []; // hold the class data
        $classResult = $conn->query("SELECT classId, className FROM Class"); // get all class IDs and names from the Class table
        if ($classResult && $classResult->num_rows > 0) {
            while($class = $classResult->fetch_assoc()) {
                $classList[$class['classId']] = $class['className'];
            }
        }

        //the add New Pupil and Remove Pupil dropdowns
        echo "<div class='row'>";
        echo "<details class='form-details'>";
        echo "<summary class='button'>Add New Pupil</summary>";
        echo "<form method='POST' action='' class='card'>";
        echo "<label>Full Name:<br><input type='text' name='pupilNames' class='input-boxes' required></label>";
        echo "<label>Address:<br><input type='text' name='pupilAddress' class='input-boxes' required></label>";
        echo "<label>Medical Info:<br><input type='text' name='medicalInformation' class='input-boxes'></label>";
        echo "<label>Class:<br><select name='pupilClassId' class='input-boxes' required>";
        echo "<option value=''>Select a class</option>";
        foreach ($classList as $cid => $cname) {
            echo "<option value='" . ($cid) . "'>" . ($cname) . "</option>";
        }
        echo "</select></label>";
        echo "<input type='submit' name='addPupil' value='Add Pupil' class='button'>";
        echo "</form>";
        echo "</details>";

        echo "<details class='form-details'>";
        echo "<summary class='button'>Remove Pupil</summary>";
        echo "<form method='POST' action='' class='card'>";
        echo "<label for='pupilId'>Select Pupil:<br>";
        echo "<select id='pupilId' name='pupilId' class='input-boxes' required>";
        $pupilListResult = $conn->query("SELECT pupilId, pupilNames FROM Pupils");
        if ($pupilListResult && $pupilListResult->num_rows > 0) {
            while($pupil = $pupilListResult->fetch_assoc()) {
                echo "<option value='" . ($pupil['pupilId']) . "'>" . ($pupil['pupilId']) . " - " . ($pupil['pupilNames']) . "</option>";
            }
        }
        echo "</select></label>";
        echo "<input type='hidden' name='removePupil' value='1'>";
        echo "<input type='submit' value='Remove Pupil' class='button'>";
        echo "</form>";
        echo "</details>";
        echo "</div>";


        ///////////////////////////
        /// Display Pupils Code ///
        ///////////////////////////
        //get all classes for the dropdown filter
        //fetch all classes from datebase into $classList
        $classList = []; //hold the class data
        $classResult = $conn->query("SELECT classId, className FROM Class"); //get all class IDs and names from the Class table

        if ($classResult && $classResult->num_rows > 0) {
            while($class = $classResult->fetch_assoc()) {
                $classList[$class['classId']] = $class['className'];
            }
        }

        //GET takes the number from the browser URL to then filter the pupils by class, when teh user selects a class from the dropdown and submits the form
        $selectedClassId = isset($_GET['filterClassId']) ? intval($_GET['filterClassId']) : 0;

        //the SQL query to get pupils
        if ($selectedClassId > 0) {
            //when a class is selected, only show pupils in that class
            $sql = "SELECT pupilId, pupilNames, pupilAddress, medicalInformation, classId FROM Pupils WHERE classId = $selectedClassId";
        } else {
            //if not show all pupils
            $sql = "SELECT pupilId, pupilNames, pupilAddress, medicalInformation, classId FROM Pupils";
        }
        $result = $conn->query($sql);

        // Show the filter dropdown above the table
        echo "<form method='GET' action='' style='margin-bottom:16px;'>";
        echo "<label for='filterClassId' style='color:#fff;font-weight:bold;'>View Pupils by Class: </label>";
        echo "<select name='filterClassId' id='filterClassId' class='input-boxes' style='width:auto;'>";
        echo "<option value='0'" . ($selectedClassId == 0 ? ' selected' : '') . ">All Classes</option>";
        //creates an option in the dropdown for each class
        foreach ($classList as $cid => $cname) {
            $selected = ($selectedClassId == $cid) ? 'selected' : '';
            echo "<option value='" . ($cid) . "' $selected>" . ($cname) . "</option>";
        }
        echo "</select> ";
        echo "<input type='submit' value='Filter' class='button'>";
        echo "</form>";

        //display any success messages after adding, removing or editing a pupil page refresh
        if (isset($_SESSION['success'])) {
            echo "<div class='card'>
                    <p class='success'>" . $_SESSION['success'] . "</p>
                </div>";
            unset($_SESSION['success']);
        }

        //////////////////////////////
        // Display the pupils table///
        //////////////////////////////
        if ($result->num_rows > 0) {
            echo "<table class=card>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Address</th>
                        <th>Medical Info</th>
                        <th>Edit</th>
                    </tr>";
            while($row = $result->fetch_assoc()) {//loop through each pupil and create a table row
                $pupilId = $row['pupilId'];//Gets the pupils ID
                echo "<tr>";
                echo "<td>{$pupilId}</td>";
                echo "<td>{$row['pupilNames']}</td>";
                $currentClass = isset($classList[$row['classId']]) ? $classList[$row['classId']] : 'None'; //finds the pupils class name, or says None
                echo "<td>" .($currentClass) . "</td>";
                echo "<td>{$row['pupilAddress']}</td>";
                echo "<td>{$row['medicalInformation']}</td>";

                // shows edit form if requested for this pupil
                $showEdit = isset($_GET['editPupilId']) && $_GET['editPupilId'] == $pupilId;
                echo "<td>"
                    . "<form method='get' action=''>"
                    . "<input type='hidden' name='editPupilId' value='{$pupilId}'>"
                    . "<button type='submit' class='dots-container' style='background:none;border:none;padding:0;cursor:pointer;'>"
                    . "<div class='dot'></div>"
                    . "<div class='dot'></div>"
                    . "<div class='dot'></div>"
                    . "</button>"
                    . "</form>";
                //shows the edit form if the 3 dots buttons are clicked
                if ($showEdit) {
                    echo "<div class='card edit-form-popup' style='padding:10px'>"
                        . "<form method='post' action=''>"
                        . "<input type='hidden' name='editPupilId' value='{$pupilId}'>"
                        . "<label>Name:<br><input type='text' name='editPupilNames' value='" . ($row['pupilNames']) . "' required></label><br>"
                        . "<label>Address:<br><input type='text' name='editPupilAddress' value='" . ($row['pupilAddress']) . "' required></label><br>"
                        . "<label>Medical Info:<br><input type='text' name='editMedicalInformation' value='" . ($row['medicalInformation']) . "'></label><br>"
                        . "<label>Class:<br><select name='editPupilClassId' class='input-boxes' required>";
                    //for each class, creates an option in the dropdown
                    foreach ($classList as $cid => $cname) {
                        $selected = ($row['classId'] == $cid) ? 'selected' : '';
                        echo "<option value='{$cid}' {$selected}>{$cname}</option>";
                    }
                    //close the select and form
                    echo "</select></label><br>"
                        . "<input type='submit' name='editPupil' value='Save' class='button'>"
                        //cancel button to close the edit form
                        . "<a href='?" . http_build_query(array_diff_key($_GET, ['editPupilId'=>1])) . "' class='button' style='margin-left:8px;text-decoration:none;color:black;'>Cancel</a>"
                        . "</form>"
                        . "</div>";
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='card'><p>No pupils found in this class
            .</p></div>";
        }

        ///////////////////////////
        /// Edit Pupil Code ////////
        ///////////////////////////
        //when the edit is done save is pressed then
        if (
            $_SERVER["REQUEST_METHOD"] === "POST" &&
            isset($_POST['editPupil']) && isset($_POST['editPupilId']) && isset($_POST['editPupilNames']) && isset($_POST['editPupilAddress']) && isset($_POST['editPupilClassId'])
        ) {
            $editPupilId = intval($_POST['editPupilId']);
            $editPupilNames = $_POST['editPupilNames'];
            $editPupilAddress = $_POST['editPupilAddress'];
            $editMedicalInformation = isset($_POST['editMedicalInformation']) ? $_POST['editMedicalInformation'] : '';
            $editPupilClassId = intval($_POST['editPupilClassId']);
            // validation
            if (preg_match('/[0-9]/', $editPupilNames)) {
                echo "<div class='card'>
                        <p class='error'>Full Name must not contain numbers.</p>
                    </div>";
            } elseif ($editPupilClassId <= 0) {
                echo "<div class='card'>
                        <p class='error'>Please select a class.</p>
                    </div>";
            } else {
                $stmt = $conn->prepare("UPDATE Pupils SET pupilNames=?, pupilAddress=?, medicalInformation=?, classId=? WHERE pupilId=?");
                $stmt->bind_param("sssii", $editPupilNames, $editPupilAddress, $editMedicalInformation, $editPupilClassId, $editPupilId);              
                if ($stmt->execute()) {
                    //uses PHP header redirect to refresh the page after successful update and show success message
                    $_SESSION['success'] = "Pupil updated successfully.";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit();
                                    
                } else {
                    echo "<div class='card'>
                            <p class='error'>Error: " . $stmt->error . "</p>
                        </div>";
                }
                $stmt->close();
            }
        }

        $conn->close();   //close the database connection
        ?>
    </body>

    <footer>
        <div style="max-width:900px;margin:40px auto;margin-top:24px;">
            <a href="main.php" class="button">Back to Dashboard</a>
        </div>
    </footer>
</html>