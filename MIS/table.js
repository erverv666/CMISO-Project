document.addEventListener("DOMContentLoaded", () => {
    const filterDropdown = document.getElementById("filterTitle");
    const viewSortedRequests = document.getElementById("viewSortedRequests");

    // Fetch stored requests from localStorage
    let requests = JSON.parse(localStorage.getItem("requests")) || [];

    // Populate dropdown with unique document titles
    if (filterDropdown) {
        let uniqueTitles = [...new Set(requests.map(request => request.documentTitle))];

        uniqueTitles.forEach(title => {
            const option = document.createElement("option");
            option.value = title;
            option.textContent = title;
            filterDropdown.appendChild(option);
        });

        // Apply filter when dropdown changes
        filterDropdown.addEventListener("change", () => {
            const title = filterDropdown.value;
            const queryParams = new URLSearchParams({ title }).toString();
            viewSortedRequests.href = `table.html?${queryParams}`;
        });
    }

    loadRequests(); // Load table data on page load
});

// 🚀 Function to load and display requests dynamically
function loadRequests() {
    const urlParams = new URLSearchParams(window.location.search);
    const selectedTitle = urlParams.get("title");

    let requests = JSON.parse(localStorage.getItem("requests")) || [];
    let filteredRequests = selectedTitle ? requests.filter(req => req.documentTitle === selectedTitle) : requests;

    let docTitle = document.getElementById("docTitle");
    let officeCell = document.getElementById("officeCell");
    let classificationCell = document.getElementById("classificationCell");
    let transactionTypeCell = document.getElementById("transactionTypeCell");
    let whoMayAvailCell = document.getElementById("whoMayAvailCell");
    let checklistCell = document.getElementById("checklistCell");
    let tableBody = document.getElementById("requestTableBody");

    if (!tableBody) return; // Prevent errors if table is not present

    // If no matching requests, set default values
    if (filteredRequests.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-gray-500">No matching requests found.</td></tr>';
        if (docTitle) docTitle.innerText = "No Matching Requests";
        if (officeCell) officeCell.innerText = "N/A";
        if (classificationCell) classificationCell.innerText = "N/A";
        if (transactionTypeCell) transactionTypeCell.innerText = "N/A";
        if (whoMayAvailCell) whoMayAvailCell.innerText = "N/A";
        if (checklistCell) checklistCell.innerText = "N/A";
        return;
    }

    // Update main table information with first request's data
    const firstRequest = filteredRequests[0];
    if (docTitle) docTitle.innerText = firstRequest.documentTitle;
    if (officeCell) officeCell.innerText = firstRequest.officeDivision || "N/A";
    if (classificationCell) classificationCell.innerText = firstRequest.classification || "N/A";
    if (transactionTypeCell) transactionTypeCell.innerText = firstRequest.transactionType || "N/A";
    if (whoMayAvailCell) whoMayAvailCell.innerText = firstRequest.whoMayAvail || "N/A";
    if (checklistCell) checklistCell.innerText = firstRequest.checklistRequirements || "N/A";

    // Populate request table
    tableBody.innerHTML = ""; // Clear previous data
    filteredRequests.forEach((request, index) => {
        let row = document.createElement("tr");
        row.innerHTML = `
            <td class="border border-gray-400 p-2 text-center">${index + 1}</td>
            <td class="border border-gray-400 p-2 text-center">${request.transactionType}</td>
            <td class="border border-gray-400 p-2">
                1. Review the submitted request <br>
                2. Review request type: <strong>${request.transactionType}</strong>
            </td>
            <td class="border border-gray-400 p-2 text-center">None</td>
            <td class="border border-gray-400 p-2 text-center">${request.processingTime} mins</td>
            <td class="border border-gray-400 p-2">${request.personResponsible}</td>
        `;
        tableBody.appendChild(row);
    });

    // Add summary row
    let summaryRow = document.createElement("tr");
    summaryRow.classList.add("bg-blue-100", "font-semibold");
    summaryRow.innerHTML = `
        <td class="border border-gray-400 p-2 text-center" colspan="2">TOTAL:</td>
        <td class="border border-gray-400 p-2 text-center">${filteredRequests.length}</td>
        <td class="border border-gray-400 p-2 text-center">Varies</td>
        <td class="border border-gray-400 p-2"></td>
        <td class="border border-gray-400 p-2"></td>
    `;
    tableBody.appendChild(summaryRow);
}

// 🚀 Form Submission - Automatically Updates Table
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("requestForm");

    if (!form) return; // Ensure form exists before running script

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const requestData = {
            documentTitle: document.getElementById("documentTitle").value,
            officeDivision: document.getElementById("officeDivision").value,
            classification: document.getElementById("classification").value,
            transactionType: document.getElementById("transactionType").value,
            whoMayAvail: document.getElementById("whoMayAvail") ? document.getElementById("whoMayAvail").value : "Citizen",
            checklistRequirements: document.getElementById("checklistRequirements").value,
            processingTime: document.getElementById("processingTime").value,
            personResponsible: document.getElementById("personResponsible").value
        };

        let requests = JSON.parse(localStorage.getItem("requests")) || [];
        requests.push(requestData);
        localStorage.setItem("requests", JSON.stringify(requests));

        alert("Request submitted successfully!");
        
        // Redirect to table page with title filter and reload requests dynamically
        window.location.href = `table.html?title=${encodeURIComponent(requestData.documentTitle)}`;
    });
});

// 🚀 Add process to the table
document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.getElementById('addProcessBtn');
    const tableBody = document.getElementById('requestTableBody');

    addButton.addEventListener('click', () => {
        // Create a temporary editable row (not yet added to main table)
        const tempRow = document.createElement('tr');
        const tempData = [];

        for (let i = 0; i < 6; i++) {
            const td = document.createElement('td');
            td.className = "border border-gray-400 p-2";
            td.contentEditable = true;
            td.innerText = '';
            tempData.push(td);
            tempRow.appendChild(td);
        }

        const actionTd = document.createElement('td');
        actionTd.className = "border border-gray-400 p-2 text-center";

        const icon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        icon.setAttribute("xmlns", "http://www.w3.org/2000/svg");
        icon.setAttribute("viewBox", "0 0 20 20");
        icon.setAttribute("fill", "currentColor");
        icon.setAttribute("class", "w-5 h-5 text-green-600 cursor-pointer inline-block");
        icon.innerHTML = `
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V3zm10 2a1 1 0 10-2 0v4a1 1 0 002 0V5z" clip-rule="evenodd"/>
        `;

        actionTd.appendChild(icon);
        tempRow.appendChild(actionTd);
        const summaryRow = document.getElementById('summaryRow');
        tableBody.insertBefore(tempRow, summaryRow);

        icon.addEventListener('click', () => {
            // Create actual row with saved data
            const newRow = document.createElement('tr');

            tempData.forEach(cell => {
                const newTd = document.createElement('td');
                newTd.className = "border border-gray-400 p-2";
                newTd.textContent = cell.textContent;
                newTd.contentEditable = false;
                newRow.appendChild(newTd);
            });

            // Add edit icon to new row
            const actionTd = document.createElement('td');
            actionTd.className = "border border-gray-400 p-2 text-center";

            const editIcon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            editIcon.setAttribute("xmlns", "http://www.w3.org/2000/svg");
            editIcon.setAttribute("viewBox", "0 0 20 20");
            editIcon.setAttribute("fill", "currentColor");
            editIcon.setAttribute("class", "w-5 h-5 text-blue-600 cursor-pointer inline-block");
            editIcon.innerHTML = `
                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                <path fill-rule="evenodd" d="M5 13a1 1 0 011-1h3v1a1 1 0 001 1h1v1H7a2 2 0 01-2-2v-1zm11 3a1 1 0 011 1v1H3v-1a1 1 0 011-1h12z" clip-rule="evenodd"/>
            `;

            let isEditing = false;

            editIcon.addEventListener('click', () => {
                isEditing = !isEditing;

                for (let i = 0; i < 6; i++) {
                    newRow.children[i].contentEditable = isEditing;
                }

                editIcon.innerHTML = isEditing
                    ? `<path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V3zm10 2a1 1 0 10-2 0v4a1 1 0 002 0V5z" clip-rule="evenodd"/>`
                    : `<path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                       <path fill-rule="evenodd" d="M5 13a1 1 0 011-1h3v1a1 1 0 001 1h1v1H7a2 2 0 01-2-2v-1zm11 3a1 1 0 011 1v1H3v-1a1 1 0 011-1h12z" clip-rule="evenodd"/>`;

                editIcon.classList.toggle("text-green-600", isEditing);
                editIcon.classList.toggle("text-blue-600", !isEditing);
            });

            actionTd.appendChild(editIcon);
            newRow.appendChild(actionTd);

            // Replace temp row with final row
            tableBody.replaceChild(newRow, tempRow);
        });
    });
});
