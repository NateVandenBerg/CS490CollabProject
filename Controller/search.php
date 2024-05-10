<?php
header('Content-Type: application/json'); // Set the content type to application/json

include 'database_login.php';// Replace with your database connection details
$conn = DBLoginInfo();

// Check connection
if ($conn->connect_error) {
  echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
  exit; // Stop script execution after sending the error
}

// Validate and retrieve keyword from POST data
if (!isset($_POST["keyword"])) {
  echo json_encode(['error' => 'No keyword provided.']);
  exit; // Stop script execution after sending the error
}

$keyword = $_POST["keyword"];

// Escape keyword for security
$keyword = $conn->real_escape_string($keyword);
$category = $conn->real_escape_string("category");

$sql_SearchFacultyKeywords = "SELECT DISTINCT faculty.user_id, faculty.first_name, faculty.last_name, faculty.title, faculty.phone, faculty.email, faculty.office, faculty.username, faculty.keywords, faculty.department_id, faculty.research_collab, faculty.presentation, faculty.share_resource, faculty.description, faculty.collab_post, faculty.active, department.department_name, college.college_name FROM faculty
            JOIN faculty_keyword_relation ON faculty.user_id = faculty_keyword_relation.user_id
            JOIN faculty_tags ON faculty_keyword_relation.keyword = faculty_tags.keyword
            JOIN department ON faculty.department_id = department.department_id
            JOIN college ON department.college_id = college.college_id 
            WHERE (faculty_tags.keyword LIKE ? OR
                   department.department_name LIKE ? OR 
                   college.college_name LIKE ? OR 
                   faculty.description LIKE ? OR
                   faculty.first_name LIKE ? OR
                   faculty.last_name LIKE ? OR
                   faculty.username LIKE ?)
                   AND faculty.active;";

$sql_SearchCollaboratorKeywords = "SELECT DISTINCT collaborator.collaborator_id, collaborator.first_name, collaborator.last_name, collaborator.title, collaborator.keywords, collaborator.description 
                                    FROM collaborator
                                    JOIN collaborator_keyword_relation ON collaborator.collaborator_id = collaborator_keyword_relation.collab_id
                                    JOIN collaborator_tags ON collaborator_keyword_relation.keyword = collaborator_tags.keyword
                                    WHERE (collaborator_tags.keyword LIKE ? OR collaborator.description LIKE ?)
                                        AND collaborator.active;";

$sql_SearchResourceKeywords = "SELECT DISTINCT resource.*, faculty.first_name, faculty.last_name, faculty.email, faculty.phone, department.department_name 
                                    FROM resource 
                                    JOIN resource_keyword_relation ON resource.resource_id = resource_keyword_relation.resource_id
                                    JOIN resource_tags ON resource_keyword_relation.keyword = resource_tags.keyword
                                    JOIN faculty ON faculty.user_id = resource.faculty_id
                                    JOIN department ON department.department_id = faculty.department_id
                                    WHERE (resource.resource_name LIKE ? OR resource_tags.keyword LIKE ? OR resource.resource_desc LIKE ?)
                                        AND resource.active;";

$sql_SearchCollabPostKeywords = "SELECT DISTINCT collab_post.*, faculty.first_name, faculty.last_name, faculty.email, faculty.phone, department.department_name 
                                    FROM collab_post
                                    JOIN collab_post_keyword_relation ON collab_post.post_id = collab_post_keyword_relation.post_id
                                    JOIN collab_post_tags ON collab_post_keyword_relation.keyword = collab_post_tags.keyword
                                    JOIN faculty ON faculty.user_id = collab_post.faculty_id
                                    JOIN department ON department.department_id = faculty.department_id
                                    WHERE (collab_post_tags.keyword LIKE ? OR collab_post.post_title LIKE ? OR collab_post.description LIKE ? OR department.department_name like ?)
                                        AND collab_post.active;";

$param = "%{$keyword}%";
$query = '';
$param_types = '';

switch ($category) {
  case 'Faculty':
    $query = $sql_SearchFacultyKeywords;
    $param_types = 'sssssss';
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types,  $param, $param, $param ,$param, $param, $param, $param);
    break;
  case 'Community Collaborators':
    $query = $sql_SearchCollaboratorKeywords;
    $param_types = 'ss';
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types,  $param, $param);
    break;
  case 'Resources':
    $query = $sql_SearchResourceKeywords;
    $param_types = 'sss';
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types,  $param, $param, $param);
    break;
  case 'Collaboration Posts':
    $query = $sql_SearchCollabPostKeywords;
    $param_types = 'ssss';
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types,  $param, $param, $param ,$param);
    break;
  default:
    $query = $sql_SearchFacultyKeywords;
    $param_types = 'sssssss';
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types,  $param, $param, $param ,$param, $param, $param, $param);
}

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check for results
if ($result->num_rows > 0) {
  $data = [];
  while($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
  echo json_encode($data);
  }else {
    echo json_encode([]);
  }

$conn->close();

?>
