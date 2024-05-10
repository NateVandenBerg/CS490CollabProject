$(document).ready(function() {
    // Fetch sidebar data on page load
    $.ajax({
        url: 'keyword_sidebar.php',
        type: 'GET',
        dataType: 'json',
        success: function(data){
            populateSidebar(data);
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error("Error fetching sidebar data:", error);
            $('#kw-table p').text('Failed to load keywords.');
        }
    });
});
function populateSidebar(sidebarData) {
    const kwSidebar = document.getElementById("keywordmenu");
    kwSidebar.innerHTML = ""; // Clear previous content

    function createFacultyKeywords() {

    }

    function createCollaboratorKeywords() {

    }

    function createResourceKeywords() {

    }

    if (sidebarData.length === 0) {
        kwSidebar.innerHTML = "<p>No keywords found.</p>";
        return;
    }

    let currentCollegeId = null;
    let currentDeptId = null;
    let collegeSection, deptList;

    sidebarData.forEach(item => {
        if (currentCollegeId !== item.college_id) {
            currentCollegeId = item.college_id;
            currentDeptId = null; // Reset current department ID

            // Create new college section as a div with a list inside
            collegeSection = document.createElement("div");
            collegeSection.id = `${item.college_id}`;
            collegeSection.className = `kwmenu_college`;
            collegeSection.innerHTML = `<h3 title="view departments..." style="cursor: pointer">${item.college_name}</h3><ul></ul>`;
            kwSidebar.appendChild(collegeSection);
        }

        if (currentDeptId !== item.department_id) {
            currentDeptId = item.department_id;

            // Create new list for the department within the current college section
            deptList = document.createElement("ul");
            deptList.className = "kwmenu_dept_list";
            deptList.innerHTML = `<li><div class="kwmenu_dept_name"><h4 style="cursor: pointer" title="view keywords: ${item.department_name}">${item.department_name}</h4></div></li><div class="kwmenu_deptkw_list"><ul class="dept_keywords"></ul></div>`;
            collegeSection.querySelector('ul').appendChild(deptList); // Append the list to the college section
        }

        // Add keyword item to the current department list
        const keywordItem = document.createElement("li");
        keywordItem.className = "sidebar_keyword";
        keywordItem.innerHTML = `<div class="sb_keyword"><p style="cursor: pointer" class="sb_keyword_p" data-keyword="${item.keyword}" title="search: ${item.keyword}">${item.keyword}</p></div>`;
        deptList.querySelector(".dept_keywords").appendChild(keywordItem);
    });

    $(function () {
        // Initially hide all department keyword lists
        $(".kwmenu_dept_list .dept_keywords").hide();

        // Listen for clicks on h4 elements within the department lists
        $(".kwmenu_dept_list > li > div > h4").on("click", function () {
            // Toggle the visibility of the keyword list immediately following the clicked h4
            $(this).closest("li").next().find("ul").slideToggle();
        });
    });

    $(function () {
        // Initially hide all department lists
        $(".kwmenu_college > ul").hide();

        // Listen for clicks on h3 elements within the college sections
        $(".kwmenu_college > h3").on("click", function () {
            // Toggle the visibility of the department lists following the clicked h3
            $(this).next("ul").slideToggle();
        });
    });

    $(document).ready(function () {
        $('.sb_keyword_p').click(function () {
            // Retrieve the keyword from the data attribute of the clicked element
            var keyword = $(this).data('keyword');

            // Send the keyword to a server-side script (e.g., 'queryDatabase.php') using AJAX
            $.ajax({
                type: "POST",
                url: "search.php",
                data: {keyword: keyword},
                success: async function (response) {
                    // Handle the response from the server
                    console.log(response);
                    try {
                        const data = await performSearch(keyword);
                        updateSearchResults(data, keyword);
                    } catch (error) {
                        console.error("Error:", error);
                        alert("An error occurred during the search. Please try again.");
                    }
                },
                error: function (xhr, status, error) {
                    // Handle any errors here
                    console.error(error);
                }
            });
        });
    });
}