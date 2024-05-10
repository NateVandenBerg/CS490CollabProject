<?php
//include 'database_login.php';
error_reporting(0);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
//$conn = DBLoginInfo();
//
//if (!$conn) {
//header('Content-Type: application/json');
//    echo json_encode(['error' => 'Failed to connect to the database.']);
//    exit;
//}

function getID($conn, $email, $formID) {
    if (!isset($email) || !trim($email)) {
        return ['error' => 'No email provided.'];
    }
    if (!isset($formID) || !trim($formID)) {
        return ['error' => 'No formID provided.'];
    }

    // Assuming a limited list of valid formIDs
    $validFormIDs = ['collab_post', 'resource', 'faculty', 'collaborator'];
    if (!in_array($formID, $validFormIDs)) {
        return ['error' => 'Invalid formID provided.'];
    }

    //$formID === 'faculty' ? 'user_id' : $formID . '_id';
    switch ($formID) {
        case 'collaborator':
            $queryField = 'collaborator_id';
            $table = 'collaborator';
            break;
        case 'faculty':
        case 'resource':
        case 'collab_post':
            $queryField = 'user_id';
            $table = 'faculty';
            break;
        default:
            return ['error' => 'PANIC!! SHOULD NOT BE HERE!!'];
    }

    $query = "SELECT $queryField FROM $table WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        return ['error' => 'Failed to prepare statement.'];
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    switch ($formID) {
        case 'collaborator':
        case 'faculty':
            if($row){
                return ['error' => 'email already in use'];
            } else {
                return['success' => true];
            }
            break;
        case 'resource':
        case 'collab_post':
            if ($row) {
                return ['success' => true, 'user_id' => $row['user_id']];
            } else {
                return ['error' => 'Faculty email not found in records.'];
            }
            break;
        default:
            return ['error' => 'PANIC!! SHOULD NOT BE HERE TOO!!'];
    }
}
?>
