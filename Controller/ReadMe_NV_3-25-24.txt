Please read all before modifying
new files:
    collab.sql <----Our database
        - HOW TO UPLOAD THIS DATABASE:
            1)Log into phpMyAdmin
            2)Select the database you want to import to on the left pane
            3)Click the Import tab in the top menu
            4)Under the File to import section, click Browse and locate the .sql file you want to import
            5)Check or uncheck the boxes for Partial import and Other options
            6)From the Format dropdown menu, choose SQL
            7)Click Go at the bottom to import the database
            8)A message confirming the import appears on your screen
            URL TO UPLOAD FILE: http://localhost/phpMyAdmin/index.php?route=/server/import
        - contains the current database design
        - because of new design, sql queries are a little more complex
        - refer to "common_DB_Queries.txt" to get an understanding of how to make queries
    create_department_table.py
        - NOT USED IN BY THIS PROJECT
        - used to modify the original database tables
            - E.G. make the database more query friendly
    database_login.php
        - contains the login info to access the database
        - CHANGE CREDENTIALS FOR YOUR DATABASE
        - ADD TO ANY FILE THAT NEEDS TO ACCESS DATABASE
            - E.G.
            "
                header('Content-Type: application/json'); // Set the content type to application/json

                include 'database_login.php';// Replace with your database connection details
                $conn = DBLoginInfo();
            "
            - See search.php for to get started
    FACULTY_TABLE.txt
        - NO LONGER USED. MAY DELETE LATER
    index.html
        - updated query statement ($stmt) to work with new database design
        - NEW FEATURES:
            - updated search results table
                - included feature so that user can now click on a result to view keywords associated with the result
                - see script.js "$(function(){...}"
            - added sidebar to show all keywords
                - organized by colleges -> departments -> keywords
                - click on department to view associated keywords
                    - NEED TO ADD FEATURE:
                        - click on keywords to perform database query and display results
    keyword_sidebar.php
        - performs query to get sidebar data
        - used in index.html
    KeywordSideBar.js
        - populates sidebar with json from keyword_sidebar.php
        - used in index.html

FEATURES NEEDED TO BE ADDED
    - create tables:
        collaborator
            .user_id
            .first_name
            .last_name
            .description
            .keywords
            .email ****MUST ENSURE CANNOT BE VIEWED ON WEBPAGE****
            [additional elements and tables?]
        collab_keyword_relation
            .keyword
            .collab_id
        collab_tags
            .keyword

        resource
            .id
            .name
            .description
            .keywords
            .faculty_id = faculty.user_id
            [additional elements and tables?]
        resource_keyword_relation
            .keyword
            .resource_id = resource.id
        resource_tag
            .keyword

    - keyword sidebar:
        - click on keywords to perform database query and display results
            - update and modify
                keyword_sidebar.php
                KeywordSideBar.js

    - create_account_*****.php AND/OR .html page
        - 2 types:
            - faculty
            - collaborator
        - MUST INCLUDE QUERY TO ADD NEW ACCOUNT
            - VERIFY THAT ACCOUNT DOES NOT EXIST
                - use username or email?
        - faculty_create_account (added to faculty table)
            - include an "add resource feature"
                - resource name
                - resource description
                - reference to faculty member that has access
                    - use user_id column in faculty table
            - info needed from new user
                - .first_name
                - .last_name
                - .email
                - .username
                - .department (.department = department.department_id)
                    - give selector from departments table
                        - "other"
                            - create new department in table
                    - .college (.department -> department table -> department.college_id = college.college_id)
                            faculty.department_id = department.department_id ... department.college_id = college.college_id
                    - give selector from college table
                - .description
                    - NEED TO ADD:
                        - user adds description
                            - description sent to server
                            - server AI process analyzes description
                                - generates keywords based on description
                                - sends keywords back to user
                            - give user ability to review keywords
                                - removed unwanted keywords
                                - manually add additional keywords
                            - after user review and approval
                                - keywords added to database
                                    faculty_tags
                                    faculty_keyword_relation
                - .keywords
                - .phone
                - .office
                - optional add resources page
                    - keyword analyzer server
                    - user review keywords
                    - add resource to resource table
                        - resource
                            .name
                            .id
                            .description
                            .keywords
                            .faculty_id = faculty.user_id
        - collab_create_account
             - info needed from collaborator
                - info needed from new user
                                - .first_name
                                - .last_name
                                - .email
                                - .username
