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
            <h1>Sign Up</h1>
            <label> Email:            <input type = "email" name = "email" require></label>
            <br>
            <label> Password: <input type ="password" name="pass" require></label>
            <br>
            <input class="button" type="submit" value ="Log In" >
        </form>

        <?php
                $email = $_POST["email"];
                $password = $_POST["pass"];
                if (strlen($password) < 8)
                {
                    echo("Password must have at least 8 characters");
                }
                else
                {
                    echo("Your email is $email <br>");
                    echo("Your Password is $password <br>");
                }
        ?>
    </body>
</html>