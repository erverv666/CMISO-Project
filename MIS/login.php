<?php
session_start();
include("db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    // Prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['password'] === $password) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['office'] = $user['office'];
        header("Location: services.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
    <div class="text-center mb-6">
      <img src="San_Pablo_City_Laguna_seal.svg" class="w-20 h-20 mx-auto mb-2" />
      <h2 class="text-xl font-bold">Login</h2>
      <p class="text-gray-600 text-sm">Enter your office account</p>
    </div>

    <?php if ($error): ?>
      <p class="text-red-500 text-sm text-center mb-4"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-4">
        <label for="username" class="block font-semibold text-left mb-1">Username</label>
        <input type="text" name="username" required class="w-full border p-2 rounded" placeholder="Username" />
      </div>

      <div class="mb-6">
        <label for="password" class="block font-semibold text-left mb-1">Password</label>
        <input type="password" name="password" required class="w-full border p-2 rounded" placeholder="Password" />
      </div>

      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white w-full p-2 rounded font-semibold">
        Login
      </button>
    </form>
  </div>
</body>
</html>
