<html>
    <head>
        <title>Sign Up</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <form class="card" method="POST" action="">
            <h1> Sign Up </h1>  
            <h3> Teacher Registration: </h3>
            <label> First Name: &nbsp;<input type = "text" name = "fName" required></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   
            <label> Last Name: &nbsp;<input type = "text" name = "lName" required></label>
            <br><br>
            <label> Date of Birth: &nbsp;<input type ="date" name="dob" required> </label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label> Email: &nbsp; <input type = "email" name = "email" required></label>
            <br><br>
            <label> Password: &nbsp;<input type="password" name="pass" required></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label> Phone Number: &nbsp;<input type = "number" name = "pNumber" required></label>
            <br><br>
            <label> Address: &nbsp;<input type = "text" name = "address" required></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label> Annual Salary: &nbsp;<input type = "number" step="0.01" name = "annualSalary" required></label>
            <br><br>
            <label> Background Check Passed: &nbsp;
                <select name="backgroundCheck" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </label>
            <br>
            <input class="button" type="submit" value="Sign Up">
        </form>

        <h5 style="text-align:center;">Already have an account? <a href="login.php">Log in here</a></h5>

        <?php
        include "db_connect.php"; //connect to the database file

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $fName = isset($_POST["fName"]) ? trim($_POST["fName"]) : "";
                $lName = isset($_POST["lName"]) ? trim($_POST["lName"]) : "";
                $dob = isset($_POST["dob"]) ? trim($_POST["dob"]) : "";
                $email = isset($_POST["email"]) ? trim($_POST["email"]) :"";
                $pass = isset($_POST["pass"]) ? trim($_POST["pass"]) : "";
                $pNumber = isset($_POST["pNumber"]) ? trim($_POST["pNumber"]) :"";
                $address = isset($_POST["address"]) ? trim($_POST["address"]) : "";
                $annualSalary = isset($_POST["annualSalary"]) ? trim($_POST["annualSalary"]) : 0;
                $backgroundCheck = isset($_POST["backgroundCheck"]) ? intval($_POST["backgroundCheck"]) : 0;
                $errors =[];

                //need to add validation for each field here
                //if fnmae is empty then print an error message if not then print hello name and then move on
                if (empty($fName)) {
                    $errors[] = "First name is required.";
                }
                    elseif (preg_match('/[0-9]/', $fName) || preg_match('/[^A-Za-z]/', $fName)) {
                    $errors[] = "First name can only contain letters.";
                }
                // same thing for last name
                if (empty($lName)) {
                    $errors[] = "You Must enter a Last Name!";
                }
                    elseif (preg_match('/[0-9]/', $lName) || preg_match('/[^A-Za-z]/', $lName)) {
                    $errors[] = "Last name can only contain letters.";
                }
                // validate date of birth is not need since its a calendar format
                
                // email validation tho...
                if (empty($email)) {
                    $errors[] = "Email is required.";
                }
                    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format.";
                }

                // password validation is a must
                if (empty($pass)) {
                    $errors[] = "Password is required.";
                }
                elseif (strlen($pass) < 8){
                    $errors[] = "Password must be at least 8 characters long.";
                }
                    elseif (
                        !preg_match('/[0-9]/', $pass) ||
                        !preg_match('/[a-z]/', $pass) ||
                        !preg_match('/[A-Z]/', $pass) ||
                        !preg_match('/[^A-Za-z0-9]/', $pass)
                    ) {
                    $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
                }
                
                //uk password are 11 char long
                if (empty($pNumber)) {
                    $errors[] = "Phone number is required.";
                }
                    elseif (!preg_match('/^[0-9]{11}$/', $pNumber)) {
                        $errors[] = "Phone number must be exactly 11 digits.";
                    }

                // display any errors 
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo "<p style='color:red;'>$error</p>";
                    }
                } else {
                    // Insert teacher data into the teachers table, including email, dob, and password
                    $teacherNames = $fName . ' ' . $lName;
                    $stmt = $conn->prepare("INSERT INTO teachers (teacherNames, address, phoneNumber, annualSalary, backgroundCheck, Email, `D.O.B`, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssdisss", $teacherNames, $address, $pNumber, $annualSalary, $backgroundCheck, $email, $dob, $pass);
                    if ($stmt->execute()) {
                        echo "<p style='color:green;'>Sign up successful!</p>";
                    } else {
                        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                    $conn->close();
                }
            }        
        ?>
    </body>
</html>
