<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <?php
    include 'database_login.php';// Replace with your database connection details
    $conn = DBLoginInfo();

        // Get user ID from query string
        $userId = $_GET["id"];

        // Check connection
        if ($conn->connect_error) {
            echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
            die("Connection failed: " . $conn->connect_error);
        }

        // Build SQL query
        $stmt = $conn->prepare("SELECT faculty.user_id, faculty.first_name, faculty.last_name, faculty.email, faculty.phone, faculty.title, faculty.office, faculty.keywords, faculty.description, faculty.research_collab, faculty.presentation, faculty.share_resource, faculty.collab_post, department.department_name, college.college_name FROM faculty
                                        JOIN department ON faculty.department_id = department.department_id
                                        JOIN college ON department.college_id = college.college_id
                                        WHERE user_id = ?");
        $stmt->bind_param("s",  $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            echo "<h2>User Profile</h2>";
            echo "<p><b>First Name:</b> " . $user['first_name'] . "</p>";
            echo "<p><b>Last Name:</b> " . $user['last_name'] . "</p>";
            echo "<p><b>Title:</b> " . $user['title'] . "</p>";
            echo "<p><b>Department:</b> " . $user['department_name'] . "</p>";
            echo "<p><b>Office:</b> " . $user['office'] . "</p>";
            echo "<p><b>Keywords:</b> " . $user['keywords'] . "</p>";
            echo "<a title='contact user...' href = contact_form.html?id=$userId>Contact</a>";
        } else {
            echo "<h2>User not found.</h2>";
        }

        $conn->close();
    ?>
</body>
</html>
