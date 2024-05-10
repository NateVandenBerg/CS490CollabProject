<?php
$query = "SELECT  department.department_id, 
                            department.department_name, 
                            college.college_id, 
                            college.college_name 
                        FROM department
                            JOIN college ON department.college_id = college.college_id 
                        GROUP BY department.department_id, 
                                 college.college_id 
                        ORDER BY college.college_id ASC, 
                                 department.department_id ASC";

header('Content-Type: application/json'); // Set the content type to application/json

include 'db_helpers.php';
db_query($query);


