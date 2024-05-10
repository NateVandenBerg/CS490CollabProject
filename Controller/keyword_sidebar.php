<?php
$queryTagByDeptID = "SELECT faculty_tags.keyword, 
                            department.department_id, 
                            department.department_name, 
                            college.college_id, 
                            college.college_name 
                        FROM faculty_tags
                            JOIN faculty_keyword_relation ON faculty_tags.keyword = faculty_keyword_relation.keyword
                            JOIN faculty ON faculty_keyword_relation.user_id = faculty.user_id 
                            JOIN department ON faculty.department_id = department.department_id 
                            JOIN college ON department.college_id = college.college_id 
                        GROUP BY faculty_tags.keyword, 
                                 department.department_id, 
                                 college.college_id 
                        ORDER BY college.college_id ASC, 
                                 department.department_id ASC, 
                                 faculty_tags.keyword ASC;";

$queryTagByCollaborator = "SELECT collaborator_tags.keyword
                        FROM collaborator_tags
                            JOIN collaborator_keyword_relation ON collaborator_tags.keyword = collaborator_keyword_relation.keyword
                            JOIN collaborator ON collaborator_keyword_relation.collab_id = collaborator.collab_id
                        GROUP BY collaborator_tags.keyword 
                        ORDER BY collaborator_tags.keyword ASC;";

header('Content-Type: application/json'); // Set the content type to application/json

include 'db_helpers.php';
db_query($queryTagByDeptID);
