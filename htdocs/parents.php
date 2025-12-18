<?php
session_start();
//check if the user is logged in, if not send them to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: logIn.php');
    exit();
}
include 'db_connect.php';

///////////////////////////
/// Add Parent Code ///////
///////////////////////////
//checks if the request method is post and the addParent field is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addParent'])) {
    //gets the values from the form and validates them
    $pName = trim($_POST['parentName']);
    $pAddress = trim($_POST['parentAddress']);
    $pEmail = trim($_POST['parentEmail']);
    
    //removes spaces from the phone number so it passes validation
    $pPhone = str_replace(' ', '', $_POST['telephone']);
    
    $errors = []; //array to hold any validation errors

    //validate the users inputs
    //validate Name with no numbers allowed
    if (preg_match('/[0-9]/', $pName)) {
        $errors[] = "Parent Name must not contain numbers.";
    }
    //validate email format
    if (!filter_var($pEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    //checks that the phone number is exactly 11 digits
    if (!preg_match('/^[0-9]{11}$/', $pPhone)) {
        $errors[] = "Phone number must be exactly 11 digits.";
    }

    //if no errors then add the parent to the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO Parents (parentName, parentAddress, parentEmail, telephone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $pName, $pAddress, $pEmail, $pPhone);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Parent added successfully.";
            header("Location: parents.php");
            exit();
        } else {
            $errors[] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

///////////////////////////
/// Edit Parent Code //////
///////////////////////////
//checks if the edit form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editParent'])) {
    $editId = intval($_POST['editParentId']);
    $editName = trim($_POST['editParentName']);
    $editAddress = trim($_POST['editParentAddress']);
    $editEmail = trim($_POST['editParentEmail']);
    
    //remove spaces from the phone number
    $editPhone = str_replace(' ', '', $_POST['editTelephone']);
    
    $editErrors = [];

    //validate inputs for edit
    if (preg_match('/[0-9]/', $editName)) {
        $editErrors[] = "Parent Name must not contain numbers.";
    }
    if (!filter_var($editEmail, FILTER_VALIDATE_EMAIL)) {
        $editErrors[] = "Invalid email format.";
    }
    if (!preg_match('/^[0-9]{11}$/', $editPhone)) {
        $editErrors[] = "Phone number must be exactly 11 digits.";
    }

    //update the database if there are no errors
    if (empty($editErrors)) {
        $stmt = $conn->prepare("UPDATE Parents SET parentName=?, parentAddress=?, parentEmail=?, telephone=? WHERE parentId=?");
        $stmt->bind_param("ssssi", $editName, $editAddress, $editEmail, $editPhone, $editId);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Parent details updated successfully.";
            header("Location: parents.php"); //refresh the page to clear the edit form
            exit();
        } else {
            $errors[] = "Error updating parent: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors = $editErrors; //display errors
    }
}

///////////////////////////
/// Link Parent Code //////
///////////////////////////
//links a parent to a pupil in the family table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['linkParent'])) {
    $parentId = intval($_POST['linkParentId']);
    $pupilId = intval($_POST['linkPupilId']);
    
    //check if this link already exists so there are no duplicates
    $check = $conn->prepare("SELECT * FROM Family WHERE parentId = ? AND pupilId = ?");
    $check->bind_param("ii", $parentId, $pupilId);
    $check->execute();

    //if no result found, create the link
    if ($check->get_result()->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO Family (parentId, pupilId) VALUES (?, ?)");
        $stmt->bind_param("ii", $parentId, $pupilId);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Parent linked to pupil successfully.";
            header("Location: parents.php");
            exit();
        }
        $stmt->close();
    } else {
        $errors[] = "This parent is already linked to that pupil.";
    }
    $check->close();
}

///////////////////////////
/// Remove Parent Code ////
///////////////////////////
//checks if the request method is post and removeParent field is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['removeParent'])) {
    $delId = intval($_POST['removeParentId']);
    
    //delete links first to maintain database integrity
    $stmt1 = $conn->prepare("DELETE FROM Family WHERE parentId = ?");
    $stmt1->bind_param("i", $delId);
    $stmt1->execute();
    $stmt1->close();

    //then delete the parent from the parents table
    $stmt2 = $conn->prepare("DELETE FROM Parents WHERE parentId = ?");
    $stmt2->bind_param("i", $delId);
    if ($stmt2->execute()) {
        $_SESSION['success'] = "Parent deleted successfully.";
        header("Location: parents.php");
        exit();
    }
    $stmt2->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Parents</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
        <h1 class="card">Manage Parents & Guardians</h1>
        
        <?php
        //display any errors or success messages
        if (!empty($errors)) {
            echo "<div class='card' style='background:#ff000022;'>
                    <p><b>Error:</b> " . implode('<br>', $errors) . "</p>
                </div>";
        }
        if (isset($_SESSION['success'])) {
            echo "<div class='card'>
                    <p class='success'>" . $_SESSION['success'] . "</p>
                </div>";
            unset($_SESSION['success']);
        }
        ?>

        <!-- forms to add, link, and remove parents -->
        <div class="row">
            <details class="form-details">
                <summary class="button">Add New Parent</summary>
                <form method="POST" action="" class="card">
                    <label>Full Name:<br><input type="text" name="parentName" class="input-boxes" required></label>
                    <label>Address:<br><input type="text" name="parentAddress" class="input-boxes" required></label>
                    <label>Email:<br><input type="email" name="parentEmail" class="input-boxes" required></label>
                    <label>Phone Number:<br><input type="text" name="telephone" class="input-boxes" required></label>
                    <input type="submit" name="addParent" value="Add Parent" class="button">
                </form>
            </details>

            <details class="form-details">
                <summary class="button">Link Parent to Pupil</summary>
                <form method="POST" action="" class="card">
                    <label>Select Parent:<br>
                        <select name="linkParentId" class="input-boxes" required>
                            <?php
                            //php to show all parent options for the dropdown
                            $pResult = $conn->query("SELECT parentId, parentName, parentEmail FROM Parents");
                            while ($row = $pResult->fetch_assoc()) {
                                $name = $row['parentName'] ? $row['parentName'] : $row['parentEmail'];
                                echo "<option value='{$row['parentId']}'>{$name} (ID: {$row['parentId']})</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <label>Select Pupil:<br>
                        <select name="linkPupilId" class="input-boxes" required>
                            <?php
                            //php to show all pupil options for the dropdown
                            $pupilResult = $conn->query("SELECT pupilId, pupilNames FROM Pupils");
                            while ($row = $pupilResult->fetch_assoc()) {
                                echo "<option value='{$row['pupilId']}'>{$row['pupilNames']}</option>";
                            }
                            ?>
                        </select>
                    </label>
                    <input type="submit" name="linkParent" value="Link Parent" class="button">
                </form>
            </details>

            <details class="form-details">
                <summary class="button">Remove Parent</summary>
                <form method="POST" action="" class="card">
                    <label>Select Parent:<br>
                        <select name="removeParentId" class="input-boxes" required>
                            <?php
                            //reuse parent query logic for the remove dropdown
                            $pResult = $conn->query("SELECT parentId, parentName, parentEmail FROM Parents");
                            if ($pResult->num_rows > 0) {
                                while ($row = $pResult->fetch_assoc()) {
                                    $name = $row['parentName'] ? $row['parentName'] : $row['parentEmail'];
                                    echo "<option value='{$row['parentId']}'>{$name} (ID: {$row['parentId']})</option>";
                                }
                            }
                            ?>
                        </select>
                    </label>
                    <input type="hidden" name="removeParent" value="1">
                    <input type="submit" value="Remove Parent" class="button">
                </form>
            </details>
        </div>

        <div>
        <h2>Registered Parents</h2>
        <table class="card">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Children (Linked)</th>
                <th>Edit</th>
            </tr>
            <?php
            //query to get parent details and linked children
            $sql = "SELECT p.parentId, p.parentName, p.parentAddress, p.parentEmail, p.telephone, 
                    GROUP_CONCAT(pu.pupilNames SEPARATOR ', ') as children
                    FROM Parents p 
                    LEFT JOIN Family f ON p.parentId = f.parentId 
                    LEFT JOIN Pupils pu ON f.pupilId = pu.pupilId 
                    GROUP BY p.parentId";
            
            $result = $conn->query($sql);
            
            //check if edit is requested
            $editParentId = isset($_GET['editParentId']) ? intval($_GET['editParentId']) : 0;
            
            //loops through each parent and displays in a table row
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['parentId'] . "</td>";
                    echo "<td>" . ($row['parentName'] ? $row['parentName'] : 'Unknown') . "</td>";
                    echo "<td>" . $row['parentAddress'] . "</td>";
                    echo "<td>" . $row['parentEmail'] . "</td>";
                    echo "<td>" . $row['telephone'] . "</td>";
                    echo "<td>" . ($row['children'] ? $row['children'] : '<i>None</i>') . "</td>";
                    
                    //Edit button with three dots to open the edit form
                    echo "<td>
                        <form method='get' action=''>
                            <input type='hidden' name='editParentId' value='" . $row['parentId'] . "'>
                            <button type='submit' class='dots-container' style='background:none;border:none;'>
                                <div class='dot'></div>
                                <div class='dot'></div>
                                <div class='dot'></div>
                            </button>
                        </form>";
                        
                    //checks if the current parent row is the one being edited
                    if ($editParentId == $row['parentId']) {
                        echo "<div class='card edit-form-popup' style='padding:10px; margin-top:10px;'>
                                <form method='post' action=''>
                                    <input type='hidden' name='editParentId' value='" . $row['parentId'] . "'>
                                    <label>Name:<br><input type='text' name='editParentName' value='" . $row['parentName'] . "' class='input-boxes' required></label>
                                    <label>Address:<br><input type='text' name='editParentAddress' value='" . $row['parentAddress'] . "' class='input-boxes' required></label>
                                    <label>Email:<br><input type='email' name='editParentEmail' value='" . $row['parentEmail'] . "' class='input-boxes' required></label>
                                    <label>Phone:<br><input type='text' name='editTelephone' value='" . $row['telephone'] . "' class='input-boxes' required></label>
                                    <input type='submit' name='editParent' value='Save' class='button'>
                                    <a href='parents.php' class='button' style='margin-left:8px;text-decoration:none;color:black;'>Cancel</a>
                                </form>
                              </div>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No parents found.</td></tr>";
            }
            ?>
        </table>
    </div>

    <footer>
        <div style="max-width:900px;margin:40px auto;margin-top:24px;">
            <a href="main.php" class="button">Back to Dashboard</a>
        </div>
    </footer>
</body>
</html>