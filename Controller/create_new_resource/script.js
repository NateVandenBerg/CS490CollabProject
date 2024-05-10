$(document).ready(function() {
    var keywords = [];

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

    $('#newResourceForm').on('submit', function(e) {
        e.preventDefault();
        let formID = "resource";

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

    // function findEmail(email,formID) {
    //     $.ajax({
    //         type: 'POST',
    //         url: '../get_faculty_id.php',
    //         data: {email: email,
    //                 formID: formID},
    //         success: function(response) {
    //             console.log("Response received:", response);
    //             if (response.facultyId) {
    //                 console.log("Faculty ID found:", response.facultyId);
    //                 createInsertData(formID, response.facultyId);
    //             } else {
    //                 console.log("Failed to find faculty ID, response was:", response);
    //                 alert('Faculty ID not found for the given email.');
    //             }
    //         },
    //         error: function() {
    //             alert('Error retrieving Faculty ID');
    //         }
    //     });
    // }

    // function createInsertData(formID, column1) {
    //     console.log("submit "+formID);
    //     var postData = {
    //         formID: formID,
    //         column1: column1,
    //         column2: $('#'+formID+'Name').val(),
    //         column3: $('#'+formID+'Desc').val(),
    //         column4: keywords
    //     };
    //
    //     console.log(postData);
    //     const url = 'create_'+formID+'.php';
    //     submitData(url,postData);
    // }

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
