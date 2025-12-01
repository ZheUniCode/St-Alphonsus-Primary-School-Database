<?php
    $f = fopen("text.txt","r");
    echo(fgets($f)."<br>");
    echo(fgets($f)."<br>");
    echo(fgets($f)."<br>");
    echo(fgets($f)."<br>");
    echo(fgets($f)."<br>");
    echo(fgets($f)."<br>");
    fclose($f);

?>



<html>    
    <body> 
        <form action = "test.php" target = "fr1" method = "Post">
            User Name: <input type = "text" name = "uname"/>
            <br>
            Password: <input type = "password" name = "pass"/>
            <br>
            <input type = "submit" value = "Log in"/>
        </form>    
        <iframe name = "fr1" style = "border-style: none"></iframe>
    </body>
<html>





