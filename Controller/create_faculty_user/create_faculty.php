<?php

include '../database_login.php';
include '../insertDataWithDynamicTypes.php';
include '../get_faculty_id.php';
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
$conn = DBLoginInfo();

try {
    if (!$conn) {
        throw new Exception('Failed to connect to the database.');
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        throw new Exception('Invalid JSON data.');
    }

    $requiredFields = ['firstName', 'lastName', 'email', 'title', 'phone', 'office', 'username', 'department', 'researchCollab', 'presentation', 'description', 'formID'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            throw new Exception("$field is required.");
        }
    }

    $escapedData = [];
    foreach ($requiredFields as $field) {
        $escapedData[$field] = $conn->real_escape_string($data[$field]);
    }

    $Id = getID($conn, $escapedData['email'], $escapedData['formID']);

    if ($Id['error'] || !$Id) {
        $errorStr = $Id['error'];
        echo json_encode(['error' => $errorStr]);
        exit;
    }

    $keywords = isset($data['keywords']) ? $data['keywords'] : [];

    $query = "INSERT INTO " . $escapedData['formID'] . " (first_name, last_name, title, phone, email, office, username, department_id, research_collab, presentation, description, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement.');
    }
    $stmt->bind_param("sssssssiiis", $escapedData['firstName'], $escapedData['lastName'], $escapedData['title'], $escapedData['phone'], $escapedData['email'], $escapedData['office'], $escapedData['username'], $escapedData['department'], $escapedData['researchCollab'], $escapedData['presentation'], $escapedData['description']);
    $result = $stmt->execute();

    if ($result) {
        $postId = $conn->insert_id;
        echo json_encode([$postId]);
        foreach ($keywords as $keyword) {
            $keyword = $conn->real_escape_string($keyword);
            $tagSql = "INSERT IGNORE INTO " . $escapedData['formID'] . "_tags (keyword) VALUES (?)";
            $tagStmt = $conn->prepare($tagSql);
            if ($tagStmt) {
                $tagStmt->bind_param("s", $keyword);
                $tagStmt->execute();
            }

            $relationSql = "INSERT INTO " . $escapedData['formID'] . "_keyword_relation (user_id, keyword) VALUES (?, ?)";
            $relationStmt = $conn->prepare($relationSql);
            if ($relationStmt) {
                $relationStmt->bind_param("is", $postId, $keyword);
                $relationStmt->execute();
            }
        }
        echo json_encode(['success' => 'Data processed successfully.']);
    } else {
        throw new Exception('Failed to execute the main insertion query.');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
