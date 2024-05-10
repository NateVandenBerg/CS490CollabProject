<?php
// Include your database login credentials
require_once 'database_login.php';

/**
 * @param string $query
 * @return void
 */
function db_query(string $query): void
{
    $conn = DBLoginInfo();

// Check connection
    if ($conn->connect_error) {
        echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
        exit; // Stop script execution after sending the error
    }
// Prepare statement
    $stmt = $conn->prepare($query);

// Execute the query
    $stmt->execute();

// Get the result
    $result = $stmt->get_result();

// Check for results
    if ($result->num_rows > 0) {
        $sidebar_data = [];
        while ($row = $result->fetch_assoc()) {
            $sidebar_data[] = $row;
        }
        echo json_encode($sidebar_data);
    } else {
        echo json_encode([]);
    }
    $conn->close();
}
