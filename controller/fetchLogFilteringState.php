<?php 

session_start();

header('Content-Type: application/json'); // sending response in json

if (isset($_SESSION["logsFilter"])){
    echo json_encode($_SESSION["logsFilter"]); // return the current session department to javascript in json
} 

?>