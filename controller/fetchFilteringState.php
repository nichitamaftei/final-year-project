<?php 

session_start();

header('Content-Type: application/json'); // sending response in json

if (isset($_SESSION["userFilter"])){
    echo json_encode($_SESSION["userFilter"]); // return the current session department to javascript in json
} 

?>