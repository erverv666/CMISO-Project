<?php
session_start();

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: index.php"); // redirect to login
  exit;
}

include("db.php"); // use the shared DB file

// Get session values
$userName = $_SESSION['username'];
$userOffice = $_SESSION['office'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newService'])) {
  $newService = trim($_POST['newService']);
  if (!empty($newService)) {
      $stmt = $conn->prepare("INSERT INTO services (office, title) VALUES (?, ?)");
      $stmt->bind_param("ss", $userOffice, $newService);
      $stmt->execute();
      $stmt->close();

      header("Location: form.php?title=" . urlencode($newService));
      exit;
  }
}

// Fetch services
$services = [];
$stmt = $conn->prepare("SELECT title FROM services WHERE office = ?");
$stmt->bind_param("s", $userOffice);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $services[] = $row['title'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .header {
      background: #1e3a8a;
      color: white;
      padding: 20px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 9999;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: background 0.3s ease;
      padding: 0 20px;
    }

    .header .logo {
      width: 75px;
      height: auto;
    }

    .header h1 {
      font-size: 1.8rem;
      font-weight: bold;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
    }

    .sidebar {
      position: fixed;
      top: 80px;
      left: 0;
      height: calc(100vh - 80px);
      width: 350px;
      background: linear-gradient(to bottom, #1e3a8a, #2563eb);
      color: white;
      padding: 2.5rem;
      display: flex;
      flex-direction: column;
      justify-content: space-evenly;
      box-shadow: 4px 0 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar a {
      display: block;
      padding: 15px;
      margin-bottom: 0.75rem;
      background: #3b82f6;
      border-radius: 0.375rem;
      font-weight: bolder;
      transition: all 0.3s ease;
      color: white;
      text-decoration: none;
    }

    .sidebar a:hover {
      background: #2563eb;
      transform: translateX(10px);
    }

    .main-container {
      display: flex;
      margin-left: 350px;
      padding-top: 100px;
      height: calc(100vh - 80px);
      padding-left: 20px;
    }

    .main-content {
      flex-grow: 1;
      padding: 2rem;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      overflow-y: auto;
      transition: box-shadow 0.3s ease;
    }

    .main-content:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-menu {
      position: absolute;
      right: 0;
      background: white;
      border: 1px solid #ddd;
      border-radius: 5px;
      width: 150px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      display: none;
    }

    .dropdown-menu a {
      display: block;
      padding: 10px;
      text-align: center;
      color: black;
      text-decoration: none;
      font-weight: bold;
    }

    .dropdown-menu a:hover {
      background-color: #f1f1f1;
    }

    .profile-dropdown:hover .dropdown-menu {
      display: block;
    }

    .service-button {
      display: block;
      background-color:rgb(5, 122, 255);
      color: white;
      padding: 12px 20px;
      margin-top: 10px;
      text-align: center;
      font-weight: bold;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .service-button:hover {
      background-color:rgb(0, 217, 255);
      transform: translateY(-2px);
    }

    .service-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .service-item {
      background-color: #f9fafb;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s ease, transform 0.3s ease;
    }

    .service-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .service-item a {
      font-weight: bold;
      color: #2563eb;
      text-decoration: none;
    }

    .service-item a:hover {
      color: #1e40af;
    }

    input[type="text"] {
      padding: 10px;
      width: 100%;
      border-radius: 8px;
      border: 1px solid #ddd;
      margin-bottom: 15px;
      font-size: 1rem;
      outline: none;
      transition: border-color 0.3s ease;
    }

    input[type="text"]:focus {
      border-color: #2563eb;
    }
  </style>
</head>
<body class="bg-gray-100">

  <!-- Header -->
  <div class="header">
    <div class="flex items-center">
      <img src="San_Pablo_City_Laguna_seal.svg" alt="Gov Logo" class="logo">
      <h1 class="ml-4">Welcome to <span class="text-blue-600"><?= htmlspecialchars($userOffice) ?></span></h1>
    </div>
    <div class="profile-dropdown">
      <button id="userDropdownBtn" class="bg-white text-gray-800 px-4 py-2 rounded-full shadow-md flex items-center gap-2 hover:bg-gray-200 transition">
        <img src="San_Pablo_City_Laguna_seal.svg" alt="User Avatar" class="w-8 h-8 rounded-full">
        <span class="font-semibold"><?= htmlspecialchars($userName) ?></span>
      </button>
      <div id="userDropdown" class="dropdown-menu hidden">
        <a href="#" class="block text-center font-bold text-gray-700 py-2">👤 <?= htmlspecialchars($userName) ?></a>
        <a href="logout.php" class="block bg-red-500 text-white font-semibold py-2 rounded-b-lg">🚪 Logout</a>
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <form method="POST">
    <input name="" type="text" placeholder="Search..." class="border p-2 rounded w-full text-gray-800 bg-white mb-2">
      <input name="newService" type="text" placeholder="Enter new service..." class="border p-2 rounded w-full text-gray-800 bg-white mb-2">
      <button type="submit" class="service-button w-full">Add Service</button>
    </form>
  </div>

  <!-- Main Content -->
  <div class="main-container">
    <div class="main-content">
      <p class="text-gray-600 text-sm">Select a service to proceed</p>
      <h3 class="text-lg font-semibold text-gray-700 mt-5 border-b-2 border-gray-300 pb-2">Available Services</h3>
      <div class="service-list">
        <?php if (empty($services)): ?>
          <p class="text-gray-500 col-span-3">No services available. Use the form to add one.</p>
        <?php else: ?>
          <?php foreach ($services as $service): ?>
            <div class="service-item">
              <a href="table.php?category=<?= urlencode($service) ?>">
                <?= htmlspecialchars($service) ?>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("userDropdownBtn").addEventListener("click", function () {
      document.getElementById("userDropdown").classList.toggle("hidden");
    });
  </script>
</body>
</html>
