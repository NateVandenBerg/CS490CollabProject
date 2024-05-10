<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <?php
        // Replace with your database connection details
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "collab";

        // Get user ID from query string
        $userId = $_GET["id"];

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Build SQL query
        //$sql = "SELECT user_id FROM collaborator WHERE user_id = " . $userId;
        $stmt = $conn->prepare("SELECT * FROM collaborator WHERE user_id =".$userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            echo "<h2>User Profile</h2>";
            echo "<p><b>First Name:</b> " . $user['first_name'] . "</p>";
            echo "<p><b>Last Name:</b> " . $user['last_name'] . "</p>";
            echo "<p><b>Title:</b> " . $user['title'] . "</p>";
            echo "<p><b>Department:</b> " . $user['department'] . "</p>";
            echo "<p><b>Office:</b> " . $user['office'] . "</p>";
            echo "<p><b>Keywords:</b> " . $user['keywords'] . "</p>";
            echo "<a href = contact_form.html?id=$userId>Contact</a>";
        } else {
            echo "<h2>User not found.</h2>";
        }

        $conn->close();
    ?>
</body>
</html>
