<?php
session_start();

header('Content-Type: application/json'); // sending response in json

if (isset($_SESSION["department"])){
    echo json_encode($_SESSION["department"]); // return the current session department to javascript in json
} 
?>