
document.addEventListener('DOMContentLoaded', function () {
    let dropdownBtnText = document.getElementById("drop-text");
    let span = document.getElementById("span");
    let icon = document.getElementById("icon");
    let list = document.getElementById("list");
    let input = document.getElementById("keyword");
    let listItems = document.querySelectorAll(".dropdown-list-item");

    dropdownBtnText.onclick = function () {
        list.classList.toggle("show");
        icon.style.rotate = "-180deg";
    };

    window.onclick = function (e) {
        if (
            e.target.id !== "drop-text" &&
            e.target.id !== "icon" &&
            e.target.id !== "span"
        ) {
            list.classList.remove("show");
            icon.style.rotate = "0deg";
        }
    };

    for (item of listItems) {
        item.onclick = function (e) {
            span.innerText = e.target.innerText;
            if (e.target.innerText == "Everything") {
                input.placeholder = "Search Anything...";
            } else {
                input.placeholder = "Search in " + e.target.innerText + "...";
            }
        };
    }
});

document.getElementById("searchForm").addEventListener("submit", async (event) => {
    event.preventDefault(); // Prevent default form submission

    const keyword = document.getElementById("keyword").value;
    const category = document.getElementById("span").innerText; // Get the currently selected category
    const searchButton = document.querySelector("button[type='submit']");
    searchButton.disabled = true; // Disable the search button to prevent multiple submissions
    console.log("Category = "+category);

    try {
        const data = await performSearch(keyword, category);
        updateSearchResults(data, keyword);
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred during the search. Please try again.");
    } finally {
        searchButton.disabled = false; // Re-enable the search button
    }
});

async function performSearch(keyword, category) {
    const response = await fetch("search.php", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ keyword: keyword, category: category })
    });

    if (!response.ok) {
        throw new Error(`Server responded with status ${response.status}`);
    }

    const responseText = await response.text(); // Get response text
    console.log(responseText); // Log it for debugging

    return JSON.parse(responseText); // Manually parse the text to JSON
}

function updateSearchResults(data, keyword) {
    let resultsTable = document.getElementById("results-table");
    resultsTable.innerHTML = `<table id="searchResults" class="collapsible-table">
                    <thead>
                    <tr>
                        <th class="dt-xradio dt-order">Last</th>
                        <th class="dt-xradio">First</th>
                        <th class="dt-xradio">Title</th>
                        <th class="dt-xradio">Department</th>
                        <th class="dt-xradio">Phone</th>
                        <th class="dt-xradio">Email</th>
                        <th class="dt-xradio">Location</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>`
    // document.getElementById("results-count").innerHTML = `Search Results for "`.keyword.`": `.data.count(row);
    const tableBody = document.getElementById("searchResults").getElementsByTagName("tbody")[0];
    tableBody.innerHTML = ""; // Clear previous results


    if (data.length > 0) {
        const resultCount = document.getElementById("results-count");
        resultCount.innerHTML = `Search Results for "${keyword}": ${data.length}`;
        let index = 1;
        data.forEach(row => {
            // Detail row
            const detailRow = document.createElement("tr");
            detailRow.title = `More info...`;
            detailRow.style = "cursor: pointer";
            detailRow.innerHTML = `<td valign="top">
                                    <span class="dirx-name2">${row.last_name}</span>
                                   </td>
                                   <td valign="top" style="white-space: nowrap;">
                                    <a title="profile for ${row.first_name} ${row.last_name}" href="https://www.csusm.edu/profiles/index.html?u=${row.username}">
                                        <span class="dirx-name2">${row.first_name}</span>
                                            <i class="icon-profile"></i>
                                    </a>
                                   </td>
                                   <td valign="top">
                                    <span class="dirx-title2">${row.title}</span>
                                   </td>
                                   <td valign="top">
                                    <span class="dirx-dept2">${row.department_name}</span>
                                   </td>
                                   <td valign="top" style="white-space: nowrap;">
                                    <span class="dirx-phone2">
                                        <a title="phone number ${row.phone}" href="tel:${row.phone}">${row.phone}</a>
                                    </span>
                                   </td>
                                   <td valign="top">
                                    <span class="dirx-email2">
                                        <a title="email address ${row.email}" href="mailto:${row.email}">${row.email}</a>
                                    </span>
                                   </td>
                                   <td valign="top">
                                    <span class="dirx-loc2">${row.office}</span>
                                   </td>`;
            const keywordRow = document.createElement("tr");
            keywordRow.innerHTML = `<td id="keywords" colspan="7" title="Hide info...">
                                        <b><p>Keywords:</b></br>
                                        ${row.keywords}</br>
                                        <b><a title="view profile" href='profile.php?id=${row.user_id}'>Profile</b></p>
                                    </td>`;

            tableBody.appendChild(detailRow);
            tableBody.appendChild(keywordRow);

            if(row.research_collab) {
                let research_collab = `Research`;
            } if(row.presentation) {
                let presentation = `Presentation`;
            } if(row.share_resource) {
                let resourceLink = `<b><a title="view resource" href='resource.php?id=${row.user_id}'>Tool/Equipment</b>`;
            } if(row.collab_post) {
                let collabPost = `<b><a title="view collaboration posting" href='collab_post.php?id=${row.user_id}'>Research Collaboration Post</b>`;
            }
            index += 1;
        });
    } else {
        tableBody.innerHTML = "<tr><td colspan=7>No results found.</td></tr>";
    }
    let slideToggleClass = ".collapsible-table"
    attachSlideToggle(slideToggleClass);

    $(function() {
        $(".collapsible-table td[colspan=7]").find("p").hide();
        $(".collapsible-table").click(function(event) {
            event.stopPropagation();
            var $target = $(event.target);
            if ($target.closest("td").attr("colspan") > 1) {
                $target.slideUp();
                // $target.closest("tr").prev().find("td:first").html("+");
            } else {
                $target.closest("tr").next().find("p").slideToggle();
                // if ($target.closest("tr").find("td:first").html() == "+")
                //     $target.closest("tr").find("td:first").html("-");
                // else
                //     $target.closest("tr").find("td:first").html("+");
            }
        });
    });


}

// Call this function whenever you dynamically load or refresh the content
// that includes your slideToggle elements
function attachSlideToggle(slideToggleClass) {
    // First, unbind any previously attached click handlers to avoid duplication
    $(slideToggleClass).off("click").on("click", function() {
        // Your slideToggle action or any other logic
        $(this).next().slideToggle();
    });
}
