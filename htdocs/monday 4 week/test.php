<?php

    function handle_input()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $uname = $_POST["uname"];
            $pass = $_POST["pass"];
            if (strlen($pass) < 8)
            {
                echo("Password must have at least 8 characters");
            }
            else
            {
                echo("Username: $uname <br>");
                echo("Password: $pass <br>");
            }
        }
    }


    



















    // if (empty($_POST["uname"]) || strlen($_POST["uname"]) > 10)
    //             {
    //                 echo("Username cannot be empty and must have less than 10 characters");
    //             }
    //             else{
    //                 $name = $_POST["uname"];
    //                 if (empty($_POST["pass"]) || strlen($_POST["pass"]) < 8)
    //                 {
    //                     echo("Password must contain at least 8 characters");
    //                 }
    //                 else{
    //                     $password = $_POST["pass"];
    //                     echo("Username: $name <br>");
    //                     echo("Password: $password <br>");
    //                 }
    //             }#

    // if ($_SERVER["REQUEST_METHOD"] == "POST")
    // {
    //     if (is_numeric($_POST["age"]))
    //     {
    //         $age = $_POST["age"];
    //         settype($age, "integer");
    //         if (is_int($age))

    //             $name = $_POST["uname"];
    //             $password = $_POST["pass"];
    //             echo("Username: $name <br>");
    //             echo("Password: $password <br>");
    //             echo("Age: $age <br>");
    //     }
    //     else
    //     {
    //         echo("Age must be a number");
    //     }


    // }


    // if ($_SERVER["REQUEST_METHOD"] == "POST")
    // {
        
    //     $name = $_POST["uname"];
    //     $password = $_POST["pass"];
    //     $upper = false;
    //     $lower = false;
    //     $digit = false;
    //     $special = false;
    //     if (strlen($password) < 8)
    //     {
    //         echo("Password must contain at least 8 characters");
    //     }
    //     else
    //     {                 
    //         for ($i = 0; $i < strlen($password); $i++)
    //         {
    //             $chr = $password[$i];
    //             if (ctype_upper($chr))
    //             {
    //                 $upper = true;
    //             }
    //             elseif (ctype_lower($chr))
    //             {
    //                 $lower = true;
    //             }
    //             elseif (is_numeric($chr))
    //             {
    //                 $digit = true;
    //             }
    //             elseif (!ctype_alnum($chr))
    //             {
    //                 $special = true;
    //             }
    //         }
    //         if ($upper && $lower && $digit && $special)
    //         {
    //             echo("Username: $name <br>");
    //             echo("Password: $password <br>");
    //         }
    //         else{
    //             echo("Password must contain upper case, lower case, digits and special characters");
    //         }
    //     }
    // }
        





?>