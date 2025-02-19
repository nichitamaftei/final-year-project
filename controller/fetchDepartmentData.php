<?php
session_start();

header("Content-Type: application/json"); // sending response in json

if (isset($_POST["request"])){ // if the client side sends a request
    $request = $_POST["request"]; // set it to the $request variable

    if (isset($_SESSION["department"])){ // (this session variable should exist regardless)

        // comparing the incoming string
        if ($request == "callMetrics"){ 
            echo json_encode($_SESSION["department"]["call_metrics"]); // return the current session department's call metrics to javascript in json
        } elseif ($request == "departmentName"){
            echo json_encode($_SESSION["department"]["name"]); // return the current session department's name to javascript in json
        } else{
            echo json_encode($_SESSION["department"]); // return the current session department to javascript in json
        }  
    } 
}
?>