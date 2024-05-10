<?php
include 'db_table_config.php';

$conn = new mysqli('hostname', 'username', 'password', 'databaseName');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Example data
$collabId = 123; // Assuming the collab_id is known
$keyword = 'Machine Learning';

$tableName = 'collaborator_keyword_relation';
$fieldDetails = $databaseTables[$tableName]['fields'];

$typeString = '';
$values = [$collabId, $keyword];
$columns = array_keys($fieldDetails);
$placeholders = array_fill(0, count($columns), '?');
$typeString = implode('', array_values($fieldDetails));

$query = "INSERT INTO $tableName (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    exit;
}

$stmt->bind_param($typeString, ...$values);
if ($stmt->execute()) {
    echo "Success!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
<?php
