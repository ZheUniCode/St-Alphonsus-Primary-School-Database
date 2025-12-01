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
            <label> First Name: &nbsp;<input type = "text" name = "fName" require></label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   
            <label> Last Name: &nbsp;<input type = "text" name = "lName" require></label>
            <br><br>
            <label>Enter Your Age: &nbsp;<input type ="number" name="age" requre> </label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label> Email: &nbsp; <input type = "email" name = "email" require></label>
            <br><br>
            <label> Phone Number: &nbsp;<input type = "number" name = "pNumber" require></label>
            <br>
            <input class="button" type="submit" value="Sign Up">
        </form>

        <?php
            $fName = $_POST["fName"];
            $lName = $_POST["lName"];
            $age = $_POST["age"];
            $email = $_POST["email"];
            $pNumber = $_POST["pNumber"];
            
        
        ?>
    </body>
</html>

    