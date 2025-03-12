<?php
session_start(); 
require_once 'conn.php'; 

session_unset();
session_destroy();

// Return JSON response for AJAX
echo json_encode(["status" => "success"]);
exit();
?>
