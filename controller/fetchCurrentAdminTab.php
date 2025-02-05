<?php 

session_start();

header('Content-Type: application/json'); // sending response in json

if (isset($_SESSION["currentTab"])){
    echo json_encode($_SESSION["currentTab"]); // return the current session department to javascript in json
} 

?>