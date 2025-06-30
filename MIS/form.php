<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Transaction</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl border border-gray-300">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <img src="San_Pablo_City_Laguna_seal.svg" alt="Government Logo" class="w-16 h-16">
            <h1 class="text-xl font-bold text-gray-800 text-center flex-1">Submit a Transaction</h1>
        </div>

        <form id="requestForm" class="space-y-4">
            <div>
                <label for="documentTitle" class="block font-semibold text-gray-700">Document Title</label>
                <input type="text" id="documentTitle" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" placeholder="Enter document title" required aria-label="Document Title">
            </div>

            <div>
                <label for="officeDivision" class="block font-semibold text-gray-700">Office or Division</label>
                <select id="officeDivision" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" required aria-label="Office or Division">
                    <option value="City Mayor's Office - MIS Office">City Mayor's Office - MIS Office</option>
                    <option value="City Planning Office">City Planning Office</option>
                    <option value="Public Safety Office">Public Safety Office</option>
                </select>
            </div>

            <div>
                <label for="classification" class="block font-semibold text-gray-700">Classification</label>
                <select id="classification" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" required aria-label="Classification">
                    <option value="Simple">Simple</option>
                    <option value="Complex">Complex</option>
                    <option value="Highly Technical">Highly Technical</option>
                </select>
            </div>

            <div>
                <label for="transactionType" class="block font-semibold text-gray-700">Type of Transaction</label>
                <select id="transactionType" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" required aria-label="Transaction Type">
                    <option value="G2C - Government to Client">G2C - Government to Client</option>
                    <option value="G2B - Government to Business">G2B - Government to Business</option>
                    <option value="G2G - Government to Government">G2G - Government to Government</option>
                </select>
            </div>

            <div>
                <label for="checklistRequirements" class="block font-semibold text-gray-700">Checklist of Requirements</label>
                <textarea id="checklistRequirements" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" placeholder="Enter required documents" required aria-label="Checklist of Requirements"></textarea>
            </div>

            <div>
                <label for="processingTime" class="block font-semibold text-gray-700">Processing Time (minutes)</label>
                <input type="number" id="processingTime" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" value="10" required aria-label="Processing Time" min="1">
            </div>

            <div>
                <label for="personResponsible" class="block font-semibold text-gray-700">Person Responsible</label>
                <input type="text" id="personResponsible" class="w-full border border-gray-400 p-2 rounded focus:ring focus:ring-blue-300" placeholder="Enter names" required aria-label="Person Responsible">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded w-full transition-all">
                Submit Request
            </button>
        </form>

        <div class="mt-6 flex justify-between">
            <a href="admin.html" class="text-blue-600 hover:underline">Admin Panel</a>
        </div>

        <!-- Success message placeholder -->
        <div id="successMessage" class="hidden mt-4 bg-green-100 text-green-700 p-4 rounded-md text-center">
            Transaction submitted successfully!
        </div>
    </div>

    <script>
        // Auto-fill the Document Title from URL
        const params = new URLSearchParams(window.location.search);
        const titleFromURL = params.get("title");
        if (titleFromURL) {
            document.getElementById("documentTitle").value = titleFromURL;
        }

        // Form submission
        document.getElementById("requestForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission for demo purpose

            // Show success message
            document.getElementById("successMessage").classList.remove("hidden");

            setTimeout(() => {
        window.location.href = "services.php"; // Redirect after a short delay
    }, 1000);
        });
    </script>
</body>
</html>
