<html>
    <head>
        <title>Log In</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
        <style>
            .button {
                border: 1px solid #ddd;
                background: #fefefe;
                border-radius: 5px;
                padding: 10px 25px;
                margin: 20px 20px;
                box-shadow: 5px 5px 0px #ffa200, inset 0 0 20px #ffffff76;
                text-align: center;
            }
            .button:hover {
                backdrop-filter: blur(50px);
                background: #fefefe26;
            }
        </style>
    </head>
    <body>
        
        <form class="card" method = "Post">
              <h1>Log In</h1>
              <label> Email: <br><input type="email" name="email" required></label>
              <br>
              <label> Password: <br><input type="password" name="pass" required></label>
              <br>
              <input class="button" type="submit" value="Log In" >
        </form>

        <?php
        //only process form if submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
            $password = isset($_POST["pass"]) ? $_POST["pass"] : '';
            $errors = [];

            //validate email
            if (empty($email)) {
                $errors[] = "Email is required.";
                //php's built-in filter to validate email format
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }

            //validate password
            if (empty($password)) {
                $errors[] = "Password is required.";
            } elseif (strlen($password) < 8) {
                $errors[] = "Password must have at least 8 characters.";
                } elseif (
                    // a function that checks if this character are not present in the password
                    !preg_match('/[A-Z]/', $password) ||      
                    !preg_match('/[a-z]/', $password) ||      
                    !preg_match('/[0-9]/', $password) ||      
                    !preg_match('/[^A-Za-z0-9]/', $password)  
                ) {
                    $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
            }

            // display errors or success
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color:red;'>$error</p>";
                }
            } else {
                
                echo "<p style='color:green;'>Login details received for $email.</p>";
                // check against database here
                //change the style later!
            }
        }
        ?>
    </body>
</html>