// 🚀 Ensure Admin Panel Loads Data Properly
document.addEventListener("DOMContentLoaded", function () {
    loadRequests();
});

// 🚀 Load & Display Transactions in Admin Panel
function loadRequests() {
    let storedRequests = localStorage.getItem("requests");
    let requests = storedRequests ? JSON.parse(storedRequests) : [];
    let tableBody = document.getElementById("adminTableBody");

    if (!tableBody) return; // Prevent errors if table is missing

    tableBody.innerHTML = "";

    if (requests.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-4 text-gray-500">No submitted requests found.</td></tr>';
    } else {
        requests.forEach((request, index) => {
            let row = document.createElement("tr");
            row.innerHTML = `
                <td class="border border-gray-400 p-2">${request.documentTitle}</td>
                <td class="border border-gray-400 p-2">${request.officeDivision}</td>
                <td class="border border-gray-400 p-2">${request.transactionType}</td>
                <td class="border border-gray-400 p-2 text-center">${request.processingTime} mins</td>
                <td class="border border-gray-400 p-2">${request.personResponsible}</td>
                <td class="border border-gray-400 p-2 text-center flex space-x-2 justify-center">
                    <!-- Edit Button -->
                    <button class="bg-yellow-500 text-white px-3 py-2 rounded text-sm flex items-center space-x-1" onclick="openEditModal(${index})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2m-1 0v16m-6-3h12m-3-6h3m-6 0h3"/>
                        </svg>
                        <span>Edit</span>
                    </button>

                    <!-- Delete Button -->
                    <button class="bg-red-500 text-white px-3 py-2 rounded text-sm flex items-center space-x-1" onclick="deleteRequest(${index})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>Delete</span>
                    </button>

                    <!-- View Button -->
                    <button class="bg-blue-500 text-white px-3 py-2 rounded text-sm flex items-center space-x-1" onclick="viewTable(${index})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h4M6 10h12M6 14h12M6 18h12"/>
                        </svg>
                        <span>View</span>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }
}

// 🚀 View Table Button Logic
function viewTable(index) {
    // Retrieve the requests from localStorage
    let requests = JSON.parse(localStorage.getItem("requests")) || [];
    let selectedRequest = requests[index];

    if (selectedRequest) {
        // Store the selected request data in localStorage
        localStorage.setItem("selectedRequest", JSON.stringify(selectedRequest));

        // Open the new window for viewing the full table or details
        window.open('viewtable.html', '_blank');
    } else {
        console.error("Request not found!");
    }
}

// 🚀 Open Edit Modal
function openEditModal(index) {
    let requests = JSON.parse(localStorage.getItem("requests")) || [];
    let request = requests[index];

    if (!request) return; // Ensure request exists

    console.log("Opening modal for index:", index); // ✅ DEBUG: Check if function runs

    // Populate modal fields
    document.getElementById("editIndex").value = index;
    document.getElementById("editDocumentTitle").value = request.documentTitle;
    document.getElementById("editOfficeDivision").value = request.officeDivision;
    document.getElementById("editTransactionType").value = request.transactionType;
    document.getElementById("editProcessingTime").value = request.processingTime;
    document.getElementById("editPersonResponsible").value = request.personResponsible;

    // Show modal
    let modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("flex"); // Ensures visibility
    } else {
        console.error("Modal element not found!"); // ✅ DEBUG
    }
}

// 🚀 Close Modal
document.getElementById("closeModal")?.addEventListener("click", () => {
    let modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }
});

// 🚀 Save Edited Request
document.getElementById("editForm")?.addEventListener("submit", function (event) {
    event.preventDefault();

    let requests = JSON.parse(localStorage.getItem("requests")) || [];
    let index = document.getElementById("editIndex").value;

    if (index === "" || isNaN(index)) return; // Prevent saving if index is invalid

    requests[index] = {
        documentTitle: document.getElementById("editDocumentTitle").value,
        officeDivision: document.getElementById("editOfficeDivision").value,
        transactionType: document.getElementById("editTransactionType").value,
        processingTime: document.getElementById("editProcessingTime").value,
        personResponsible: document.getElementById("editPersonResponsible").value
    };

    // Save back to localStorage
    localStorage.setItem("requests", JSON.stringify(requests));

    // Close modal
    let modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    // Refresh table
    loadRequests();
});

// 🚀 Delete Request
function deleteRequest(index) {
    let requests = JSON.parse(localStorage.getItem("requests")) || [];
    if (confirm("Are you sure you want to delete this request?")) {
        requests.splice(index, 1);
        localStorage.setItem("requests", JSON.stringify(requests));
        loadRequests();
    }
}

// 🚀 Ensure Form Submissions Appear in Admin Panel
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("requestForm");
    if (!form) return;

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const requestData = {
            documentTitle: document.getElementById("documentTitle").value,
            officeDivision: document.getElementById("officeDivision").value,
            classification: document.getElementById("classification").value,
            transactionType: document.getElementById("transactionType").value,
            checklistRequirements: document.getElementById("checklistRequirements").value,
            processingTime: document.getElementById("processingTime").value,
            personResponsible: document.getElementById("personResponsible").value
        };

        let requests = JSON.parse(localStorage.getItem("requests")) || [];
        requests.push(requestData);
        localStorage.setItem("requests", JSON.stringify(requests));

        alert("Request submitted successfully!");
        window.location.href = "admin.html"; // Redirect to admin panel
    });
});


// When the admin selects a document title (from dropdown or other logic)
localStorage.setItem("selectedDocumentTitle", documentTitle);  // documentTitle = selected title







