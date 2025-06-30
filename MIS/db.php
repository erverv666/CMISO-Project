<?php
// db.php
$host = 'localhost';
$dbname = 'mis_db'; // your database name
$username = 'root'; // default XAMPP username
$password = '';     // default XAMPP password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
