<?php 

session_start();

header("Content-Type: application/json"); // sending response in json

if (isset($_SESSION["callFlowEdited"])){
    echo json_encode($_SESSION["callFlowEdited"]); // return the boolean value in json
} 

?>