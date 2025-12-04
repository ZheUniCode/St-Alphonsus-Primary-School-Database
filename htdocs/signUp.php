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
            <br>
            <input class="button" type="submit" value="Sign Up">
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $fName = isset($_POST["fName"]) ? trim($_POST["fName"]) : "";
                $lName = isset($_POST["lName"]) ? trim($_POST["lName"]) : "";
                $dob = isset($_POST["dob"]) ? trim($_POST["dob"]) : "";
                $email = isset($_POST["email"]) ? trim($_POST["email"]) :"";
                $pass = isset($_POST["pass"]) ? trim($_POST["pass"]) : "";
                $pNumber = isset($_POST["pNumber"]) ? trim($_POST["pNumber"]) :"";
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
                    
                    echo "<p style='color:green;'>Login details received for $email.</p>";
                    // add to database here
                    //change the style later!
                }
            }        
        ?>
    </body>
</html>

    