<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit;
}

$userName = $_SESSION['username'];
$userOffice = $_SESSION['office'];
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!$category) {
  echo "Invalid service category.";
  exit;
}

// Fetch the current service details
$stmt = $conn->prepare("SELECT * FROM services WHERE office = ? AND title = ?");
$stmt->bind_param("ss", $userOffice, $category);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();
$stmt->close();

if (!$document) {
  echo "Service not found.";
  exit;
}

// Insert new process row
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_process'])) {
  $client_steps = $_POST['client_steps'];
  $agency_actions = $_POST['agency_actions'];
  $fees = $_POST['fees'];
  $processing_time = $_POST['processing_time'];
  $person_responsible = $_POST['person_responsible'];

  // Get latest transaction number
  $getLast = $conn->prepare("SELECT MAX(transaction_no) as max_no FROM processes WHERE service_title = ?");
  $getLast->bind_param("s", $category);
  $getLast->execute();
  $lastResult = $getLast->get_result()->fetch_assoc();
  $transaction_no = $lastResult['max_no'] + 1;
  $getLast->close();

  // Insert process
  $insert = $conn->prepare("INSERT INTO processes (service_title, transaction_no, client_steps, agency_actions, fees, processing_time, person_responsible) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $insert->bind_param("sisssis", $category, $transaction_no, $client_steps, $agency_actions, $fees, $processing_time, $person_responsible);
  $insert->execute();
  $insert->close();

  // Redirect to avoid resubmission
  header("Location: table.php?category=" . urlencode($category));
  exit;
}

// Fetch existing processes
$processes = $conn->prepare("SELECT * FROM processes WHERE service_title = ? ORDER BY transaction_no ASC");
$processes->bind_param("s", $category);
$processes->execute();
$processes = $processes->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($document['title']) ?> - Transaction Table</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen p-4">
  <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-4xl border border-gray-300">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
      <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($document['title']) ?></h1>
      <img src="San_Pablo_City_Laguna_seal.svg" alt="Government Logo" class="w-16 h-16">
    </div>

    <table class="w-full border-collapse border border-gray-400 text-sm mb-6">
      <tbody>
        <tr>
          <td class="bg-blue-100 font-semibold border p-3 w-1/4">Office or Division</td>
          <td class="border p-3"><?= htmlspecialchars($document['office']) ?></td>
        </tr>
        <tr>
          <td class="bg-blue-100 font-semibold border p-3">Classification</td>
          <td class="border p-3"><?= htmlspecialchars($document['classification']) ?></td>
        </tr>
        <tr>
          <td class="bg-blue-100 font-semibold border p-3">Type of Transaction</td>
          <td class="border p-3"><?= htmlspecialchars($document['transaction_type']) ?></td>
        </tr>
        <tr>
          <td class="bg-blue-100 font-semibold border p-3">Who may avail</td>
          <td class="border p-3"><?= nl2br(htmlspecialchars($document['who_may_avail'])) ?></td>
        </tr>
      </tbody>
    </table>

    <h2 class="text-lg font-semibold mt-6 text-gray-800">CHECKLIST OF REQUIREMENTS</h2>
    <table class="w-full border-collapse border border-gray-400 text-sm mb-6">
      <thead>
        <tr class="bg-blue-200 font-semibold text-gray-800">
          <th class="border border-gray-400 p-3 text-left">Requirement</th>
          <th class="border border-gray-400 p-3 text-left">Where to Secure</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="border border-gray-400 p-3"><?= nl2br(htmlspecialchars($document['checklist'])) ?></td>
          <td class="border border-gray-400 p-3">—</td>
        </tr>
      </tbody>
    </table>

    <div class="flex items-center justify-between mt-6 mb-2">
      <h2 class="text-lg font-semibold text-gray-800">CLIENT STEPS & AGENCY ACTIONS</h2>
    </div>

    <table class="w-full border-collapse border border-gray-400 text-sm mt-6">
      <thead>
        <tr class="bg-blue-200 font-semibold text-center text-gray-800">
          <th class="border p-3">TRANSACTION NO</th>
          <th class="border p-3">CLIENT STEPS</th>
          <th class="border p-3">AGENCY ACTIONS</th>
          <th class="border p-3">FEES</th>
          <th class="border p-3">TIME</th>
          <th class="border p-3">RESPONSIBLE</th>
          <th class="border p-3">✏️</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $processes->fetch_assoc()): ?>
          <tr class="text-center">
            <td class="border p-3"><?= htmlspecialchars($row['transaction_no']) ?></td>
            <td class="border p-3"><?= nl2br(htmlspecialchars($row['client_steps'])) ?></td>
            <td class="border p-3"><?= nl2br(htmlspecialchars($row['agency_actions'])) ?></td>
            <td class="border p-3"><?= htmlspecialchars($row['fees']) ?></td>
            <td class="border p-3"><?= htmlspecialchars($row['processing_time']) ?></td>
            <td class="border p-3"><?= htmlspecialchars($row['person_responsible']) ?></td>
            <td class="border p-3"><a href="edit_process.php?id=<?= $row['id'] ?>">✏️</a></td>
          </tr>
        <?php endwhile; ?>

        <!-- Inline Add Form -->
        <form method="POST">
          <tr class="bg-gray-100 text-center">
            <td class="border p-3">—</td>
            <td class="border p-3"><input type="text" name="client_steps" required class="border p-2 w-full"></td>
            <td class="border p-3"><input type="text" name="agency_actions" required class="border p-2 w-full"></td>
            <td class="border p-3"><input type="text" name="fees" required class="border p-2 w-full"></td>
            <td class="border p-3"><input type="number" name="processing_time" required class="border p-2 w-full"></td>
            <td class="border p-3"><input type="text" name="person_responsible" required class="border p-2 w-full"></td>
            <td class="border p-3">
              <button type="submit" name="new_process" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Add</button>
            </td>
          </tr>
        </form>

        <tr class="bg-gray-100 font-semibold text-gray-700 text-center">
          <td colspan="7" class="border p-3">End of Client Steps & Agency Actions</td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
