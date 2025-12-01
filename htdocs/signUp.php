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
                $error =[];

                //need to add validation for each field here
            }
        
        ?>
    </body>
</html>

    