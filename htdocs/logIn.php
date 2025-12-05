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

        <h5 style="text-align:center;">Don't have an account? <a href="signUp.php">Sign up here</a></h5>

        <?php
        include "db_connect.php"; //connect to the database file
        
        //only process form if submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
            $pass = isset($_POST["pass"]) ? $_POST["pass"] : '';
            $errors = [];

            //validate email
            if (empty($email)) {
                $errors[] = "Email is required.";
                //php's built-in filter to validate email format
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }

            //validate password
            if (empty($pass)) {
                $errors[] = "Password is required.";
            }

            // display errors or check database
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<p style='color:red;'>$error</p>";
                }
            } else {
                //Query the database for the user
                $stmt = $conn->prepare("SELECT * FROM teachers WHERE Email = ? AND Password = ?");
                $stmt->bind_param("ss", $email, $pass);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    header("Location: main.php");
                    exit();
                } else {
                    echo "<p style='color:red;'>Invalid email or password.</p>";
                }
                $stmt->close();
                $conn->close();
            }
        }
        ?>
    </body>
</html>