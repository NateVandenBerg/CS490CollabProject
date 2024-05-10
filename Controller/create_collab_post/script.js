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
        var email = $('#email').val();
        let formID = "resource";

        findEmail(email, formID);
        console.log(facultyID);
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
