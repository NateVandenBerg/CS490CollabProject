<?php
include '../database_login.php';
include '../insertDataWithDynamicTypes.php';
include '../get_faculty_id.php';
error_reporting(1);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json');
$conn = DBLoginInfo();

if (!$conn) {
    echo json_encode(['error' => 'Failed to connect to the database.']);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$requiredKeys = ['formID', 'column1', 'column2', 'column4'];
foreach ($requiredKeys as $key) {
    if (!isset($data[$key])) {
        echo json_encode(['error' => "$key not provided."]);
        exit;
    }
}

$allowedTables = ['collab_post', 'resource', 'faculty', 'collaborator'];
$tableName = $conn->real_escape_string($data['formID']);
if (!in_array($tableName, $allowedTables)) {
    echo json_encode(['error' => 'Invalid table name specified']);
    exit;
}

$email = $conn->real_escape_string($data["column1"]);
$nameORtitle = $conn->real_escape_string($data["column2"]);
$desc = $conn->real_escape_string($data["column3"]);
$keywords = isset($data["column4"]) ? $data["column4"] : [];

$Id = getID($conn, $email, $tableName);

if ($Id['error'] || !$Id) {
    $errorStr = $Id['error'];
    echo json_encode(['error' => $errorStr]);
    exit;
}

$query = "INSERT INTO " . $tableName . " (" . $tableName . "_name, " . $tableName . "_desc, faculty_id, active) VALUES (?, ?, ?, 1)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit;
}
$stmt->bind_param("ssi", $nameORtitle, $desc, $Id['user_id']);
$result = $stmt->execute();

if (!$result) {
    echo json_encode(['error' => 'Failed to create post: ' . $stmt->error]);
    exit;
}
$postId = $conn->insert_id;

try {
    foreach ($keywords as $keyword) {
        $tagSql = "INSERT IGNORE INTO " . $tableName . "_tags (keyword) VALUES (?)";
        $tagStmt = $conn->prepare($tagSql);

        if (!$tagStmt) {
            echo json_encode(['error' => 'Failed to prepare keyword statement: ' . $conn->error]);
            continue;  // Skip this iteration
        }
        $tagStmt->bind_param("s", $keyword);
        $tagStmt->execute();

        $relationSql = "INSERT INTO " . $tableName . "_keyword_relation (" . $tableName . "_id, keyword) VALUES (?, ?)";
        $relationStmt = $conn->prepare($relationSql);
        if ($relationStmt) {
            $relationStmt->bind_param("is", $postId, $keyword);
            $relationStmt->execute();
        }
    }
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => 'Transaction failed: ' . $e->getMessage()]);
}

echo json_encode(['success' => $tableName . ' created successfully!']);


$conn->close();
?>
