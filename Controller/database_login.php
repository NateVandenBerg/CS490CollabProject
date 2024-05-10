<?php
 function DBLoginInfo() {
     // Replace with your database connection details
     $servername = "localhost";
     $username = "root";
     $password = "root";
     $database = "collab";

        // Create connection
     $conn = new mysqli($servername, $username, $password, $database);

     return $conn;
 }

?>