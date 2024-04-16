<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
try {
 $conn = new mysqli("localhost", "johste948_reearning", "Biovus21$$", "johste948_reearning");
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>