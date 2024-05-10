$(document).ready(function() {
    let deptList = [];
    var keywords = [];
    let collegeID;

    $('#addKeyword').click(function() {
        var keyword = $('#keywordInput').val().trim();
        if (keyword) {
            keywords.push(keyword);
            $('#keywordsList').append('<span class="keyword">' + keyword + ' <button type="button" class="removeKeyword">x</button></span><br>');
            $('#keywordInput').val('');
        }
    });

    $('#keywordsList').on('click', '.removeKeyword', function() {
        var index = $(this).parent().index();
        keywords.splice(index, 1);
        $(this).parent().next('br').remove();
        $(this).parent().remove();
    });

    $('#college_id').on('change', function () {
        const selectedCollege = $(this).val();  // Get the value of the selected option
        if (selectedCollege !== collegeID && selectedCollege) {
            const dropdownOpt = $('#department_id');
            dropdownOpt.empty(); // Clear previous options
            dropdownOpt.append('<option value="" class="listOption"> --Select Department-- </option>'); // Add default option

            deptList.forEach(item => {
                if (item.college_id == selectedCollege) {
                    let listOption = $('<option>') // Create a new option element using jQuery
                        .attr('value', item.department_id)
                        .text(item.department_name);
                    dropdownOpt.append(listOption); // Append the option to the dropdown using jQuery
                }
            });
            dropdownOpt.prop("disabled", false); // Enable the dropdown
            collegeID = selectedCollege; // Update the last selected college ID
        } else {
            $('#department_id').prop("disabled", true).empty().append('<option value="">Select Department</option>'); // Disable and reset the dropdown
        }
    });

    $.ajax({
        url: '../college_department.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            // console.log(data);
            deptList = data;
            populateCollegeList(data);
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error("Error fetching department list data:", error);
            $('#college_id').append('<option value="" class="listOption"> --ERROR-- </option>');
        }
    });

    function populateCollegeList(data) {
        const collegeList = document.getElementById("college_id"); // Corrected ID to the select element
        collegeList.innerHTML = "<option value=\"\">Select college</option>\n"; // Resetting innerHTML
        let currentCollegeId = null;

        data.forEach(item => {
            if (currentCollegeId !== item.college_id) {
                currentCollegeId = item.college_id;

                // Create new college option
                let collegeName = document.createElement("option");
                collegeName.value = item.college_id;
                collegeName.textContent = item.college_name; // Use textContent instead of innerHTML when setting text
                collegeList.appendChild(collegeName);
            }
        });
    }

    $('#newResourceForm').on('submit', function(e) {
        e.preventDefault();
        var email = $('#email').val();
        let formID = "resource";

        findEmail(email, formID);
        console.log(facultyID);
    });

    $('#newFacultyUserForm').on('submit', function(e) {
        e.preventDefault();
        let formID = 'faculty';

        var postData = {
            firstName: $('#first_name').val(),
            lastName: $('#last_name').val(),
            title: $('#title').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            office: $('#office').val(),
            username: $('#username').val(),
            keywords: keywords,  // Assuming keywords are input as comma-separated values
            department: $('#department_id').val(),
            researchCollab: $('#research_collab').val(),
            presentation: $('#presentation').val(),
            description: $('#description').val()
        };

        console.log(postData);
        const url = 'create_'+formID+'.php';
        submitData(url,postData);
    })

    $('#newCollaboratorForm').on('submit', function(e) {
        e.preventDefault();
        let formID = "collaborator";

        console.log("submit "+formID);
        var postData = {
            formID: formID,
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            column1: $('#email').val(),
            column2: $('#'+formID+'Name').val(),
            column3: $('#'+formID+'Desc').val(),
            column4: keywords
        };

        console.log(postData);
        const url = 'create_'+formID+'.php';
        submitData(url,postData);
    });

    $('#collabPostForm').on('submit', function(e) {
        e.preventDefault();
        let formID = "collab_post";

        console.log("submit "+formID);
        var postData = {
            formID: formID,
            column1: $('#email').val(),
            column2: $('#'+formID+'Name').val(),
            column3: $('#'+formID+'Desc').val(),
            column4: keywords
        };

        console.log(postData);
        const url = 'create_'+formID+'.php';
        submitData(url,postData);
    });

    function submitData(url,postData) {
        console.log(postData);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(postData)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('data:', data);
                if (data.error) {
                    console.error("Error:", data.error);
                    alert('Error creating post: ' + data.error);
                } else {
                    console.log("Success:", data);
                    alert('Post created successfully!');
                    $('#keywordsList').html('');
                    keywords = [];
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('Error creating post.');
            });
    }
});
