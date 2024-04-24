<?php

$servername = "localhost";
$username = "rashed";
$password = "rashed";
$database = "inventory";

// Create connection
$connect = new mysqli($servername, $username, $password, $database);

try {
    $connect = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // set the PDO error mode to exception
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
session_start();
// Register session variables (deprecated)
// session_register('type');
// session_register('user_id');
?>
