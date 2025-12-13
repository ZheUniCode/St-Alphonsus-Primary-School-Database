
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

if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    isset($_POST['pupilNames']) && isset($_POST['pupilAddress'])
) {
    $pupilNames = $_POST['pupilNames'];
    $pupilAddress = $_POST['pupilAddress'];
    $medicalInformation = isset($_POST['medicalInformation']) ? $_POST['medicalInformation'] : '';

    // Validate Full Name and Medical Information (no numbers allowed)
    if (preg_match('/[0-9]/', $pupilNames)) {
        echo "<div class='card'><p class='error'>Full Name must not contain numbers.</p></div>";
    } elseif (preg_match('/[0-9]/', $medicalInformation)) {
        echo "<div class='card'><p class='error'>Medical Information must not contain numbers.</p></div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO Pupils (pupilNames, pupilAddress, medicalInformation) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $pupilNames, $pupilAddress, $medicalInformation);
        if ($stmt->execute()) {
            echo "<div class='card'>
                    <p class='success'>New pupil created successfully.</p>
                </div>";
        } else {
            echo "<div class='card'>
                    <p class='error'>Error: " . $stmt->error . "</p>
                </div>";
        }
        $stmt->close();
    }
}


//handle form submission to remove pupil from the database
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['pupilId']) && isset($_POST['removePupil'])
) {
    $pupilId = $_POST['pupilId'];
    // First, delete related rows in family table
    $delFamily = $conn->prepare("DELETE FROM family WHERE pupilId = ?");
    $delFamily->bind_param("s", $pupilId);
    $delFamily->execute();
    $delFamily->close();
    // Then, delete pupil
    $stmt = $conn->prepare("DELETE FROM Pupils WHERE pupilId = ?");
    $stmt->bind_param("s", $pupilId);
    if ($stmt->execute()) {
        // Redirect to avoid resubmission on refresh
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "<div class='card'>
                <p class='error'>Error: " . $stmt->error . "</p>
              </div>";
    }
    $stmt->close();
}

$sql = "SELECT pupilId, pupilNames, pupilAddress, medicalInformation FROM Pupils"; //query to select all pupils
$result = $conn->query($sql); //both execute and store the result of the query

if ($result->num_rows > 0) { //check if there are any results
    echo "<table class=card>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Medical Info</th>
            </tr>"; //table headers made
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
        echo "<div class='card'>
                <p>No pupils found.</p>
            </div>"; //message if no pupils are found
}
echo "</div>";



// html forms in one card, side by side
echo "<div class='card form-flex-group'>";
// Add New Pupil dropdown
echo "<details class='form-details'>
    <summary class='button'>Add New Pupil</summary>
    <form method='post' action='' class='pupil-form'>
        <label>Full Name:<br>
            <input type='text' name='pupilNames' required>
        </label>
        <label>Address:<br>
            <input type='text' name='pupilAddress' required>
        </label>
        <label>Medical Information:<br>
            <textarea name='medicalInformation'></textarea>
        </label>
        <input type='submit' value='Add Pupil' class='button'>
    </form>
</details>";
// Remove Pupil dropdown
echo "<details class='form-details'>
    <summary class='button'>Remove Pupil</summary>
    <form method='post' action='' class='pupil-form'>
        <label for='pupilId'>Select Pupil:<br>
            <select id='pupilId' name='pupilId' class='select-pupil' required>";
$pupilListResult = $conn->query("SELECT pupilId, pupilNames FROM Pupils");
if ($pupilListResult && $pupilListResult->num_rows > 0) {
    while($pupil = $pupilListResult->fetch_assoc()) {
        echo "<option value='{$pupil['pupilId']}'>{$pupil['pupilId']} - {$pupil['pupilNames']}</option>";
    }
}
echo "        </select>
        </label>
        <input type='hidden' name='removePupil' value='1'>
        <input type='submit' value='Remove Pupil' class='button remove-btn'>
    </form>
</details>";
echo "</div>";

$conn->close();   //close the database connection
?>
</body>
</html>